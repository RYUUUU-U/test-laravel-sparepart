<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $table      = 'orders';
    protected $primaryKey = 'id';
    public    $timestamps  = true;

    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'customer_address',
        'subtotal',
        'shipping_cost',
        'total_amount',
        'status',
        'payment_status',
        'payment_method',
        // Xendit
        'xendit_external_id',
        'xendit_invoice_id',
        'xendit_invoice_url',
        'xendit_payment_channel',
        'xendit_payment_method',
        'paid_at',
        'notes',
        // Alur baru
        'tracking_number',
        'ready_to_ship_photo_url',
        'handover_photo_url',
        'approved_at',
        'shipped_at',
        'approved_by',
        'arrived_at',
    ];

    protected $casts = [
        'subtotal'     => 'integer',
        'shipping_cost'=> 'integer',
        'total_amount' => 'integer',
        'paid_at'      => 'datetime',
        'approved_at'  => 'datetime',
        'shipped_at'   => 'datetime',
        'arrived_at'   => 'datetime',
    ];

    // ── Status Constants (5-Tier Workflow) ────────────────────────────────────
    // Old statuses kept for backward compatibility with existing orders
    const STATUS_PENDING             = 'pending';
    const STATUS_AWAITING_PAYMENT    = 'awaiting_payment';
    // New 5-tier statuses: Paid → Processed → Shipped → Arrived → Reviewed
    const STATUS_DIBAYAR             = 'dibayar';              // Paid
    const STATUS_DIPROSES            = 'diproses';             // Processing
    const STATUS_DIKIRIM             = 'dikirim';              // Shipped
    const STATUS_SUDAH_TIBA          = 'sudah_tiba';           // Arrived
    const STATUS_SELESAI             = 'selesai';              // Reviewed / Completed
    // Legacy statuses (kept for old orders)
    const STATUS_PESANAN_DISIAPKAN   = 'pesanan_disiapkan';
    const STATUS_DISETUJUI           = 'disetujui';
    const STATUS_SEDANG_DIKIRIM      = 'sedang_dikirim';
    // Terminal statuses
    const STATUS_DIBATALKAN          = 'dibatalkan';
    const STATUS_GAGAL               = 'gagal';

    const PAYMENT_UNPAID = 'unpaid';
    const PAYMENT_PAID   = 'paid';
    const PAYMENT_FAILED = 'failed';

    /**
     * Semua status yang valid.
     */
    public static function validStatuses(): array
    {
        return [
            self::STATUS_PENDING,
            self::STATUS_AWAITING_PAYMENT,
            self::STATUS_DIBAYAR,
            self::STATUS_DIPROSES,
            self::STATUS_DIKIRIM,
            self::STATUS_SUDAH_TIBA,
            self::STATUS_SELESAI,
            // Legacy
            self::STATUS_PESANAN_DISIAPKAN,
            self::STATUS_DISETUJUI,
            self::STATUS_SEDANG_DIKIRIM,
            // Terminal
            self::STATUS_DIBATALKAN,
            self::STATUS_GAGAL,
        ];
    }

    /**
     * Urutan progres 5-tier (untuk tombol "Next Status" di admin).
     */
    public static function fiveTierFlow(): array
    {
        return [
            self::STATUS_DIBAYAR     => self::STATUS_DIPROSES,
            self::STATUS_DIPROSES    => self::STATUS_DIKIRIM,
            self::STATUS_DIKIRIM     => self::STATUS_SUDAH_TIBA,
            self::STATUS_SUDAH_TIBA  => self::STATUS_SELESAI,
        ];
    }

    /**
     * Status berikutnya dalam alur 5-tier, atau null jika sudah di akhir.
     */
    public function nextStatus(): ?string
    {
        return self::fiveTierFlow()[$this->status] ?? null;
    }

    // ── Relations ─────────────────────────────────────────────────────────────

    /**
     * Customer pemilik pesanan ini (nullable untuk guest checkout).
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class, 'customer_id', 'id');
    }

    /**
     * Semua item dalam pesanan ini.
     */
    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id');
    }

    /**
     * User admin yang menyetujui pesanan.
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by', 'id_user');
    }

    // ── Helper Methods ────────────────────────────────────────────────────────

    /**
     * Cek apakah pesanan belum dibayar dan sudah lewat batas waktu (24 jam).
     * Jika ya, otomatis diubah menjadi kedaluwarsa (hangus/dibatalkan).
     */
    public function checkExpiration(): void
    {
        if ($this->status === self::STATUS_AWAITING_PAYMENT && $this->created_at->addHours(24)->isPast()) {
            $this->update([
                'status'         => self::STATUS_DIBATALKAN,
                'payment_status' => self::PAYMENT_FAILED,
            ]);
        }
    }

    /**
     * Cek apakah pesanan sudah dibayar.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === self::PAYMENT_PAID;
    }

    /**
     * Cek apakah semua item sudah direview.
     */
    public function isFullyReviewed(): bool
    {
        return $this->items->every(fn ($item) => $item->rating !== null);
    }

    /**
     * Kembalikan label status pesanan yang ramah untuk ditampilkan.
     */
    public function statusLabel(): string
    {
        // Cek expiration setiap kali label status di-render
        $this->checkExpiration();

        return match ($this->status) {
            self::STATUS_PENDING             => 'Menunggu Checkout',
            self::STATUS_AWAITING_PAYMENT    => 'Menunggu Pembayaran',
            self::STATUS_DIBAYAR             => 'Dibayar',
            self::STATUS_DIPROSES            => 'Sedang Diproses',
            self::STATUS_DIKIRIM             => 'Sedang Dikirim',
            self::STATUS_SUDAH_TIBA          => 'Sudah Tiba',
            self::STATUS_SELESAI             => 'Selesai',
            // Legacy
            self::STATUS_PESANAN_DISIAPKAN   => 'Pesanan Disiapkan',
            self::STATUS_DISETUJUI           => 'Disetujui Admin',
            self::STATUS_SEDANG_DIKIRIM      => 'Sedang Dikirim',
            // Terminal
            self::STATUS_DIBATALKAN          => 'Dibatalkan',
            self::STATUS_GAGAL               => 'Pembayaran Gagal',
            default                          => ucfirst($this->status),
        };
    }

    /**
     * Kembalikan badge Bootstrap sesuai status pesanan.
     */
    public function statusBadgeClass(): string
    {
        return match ($this->status) {
            self::STATUS_PENDING             => 'bg-secondary',
            self::STATUS_AWAITING_PAYMENT    => 'bg-warning text-dark',
            self::STATUS_DIBAYAR             => 'bg-success',
            self::STATUS_DIPROSES            => 'bg-info text-dark',
            self::STATUS_DIKIRIM             => 'bg-primary',
            self::STATUS_SUDAH_TIBA          => 'bg-info',
            self::STATUS_SELESAI             => 'bg-success',
            // Legacy
            self::STATUS_PESANAN_DISIAPKAN   => 'bg-info text-dark',
            self::STATUS_DISETUJUI           => 'bg-primary',
            self::STATUS_SEDANG_DIKIRIM      => 'bg-info',
            // Terminal
            self::STATUS_DIBATALKAN          => 'bg-danger',
            self::STATUS_GAGAL               => 'bg-danger',
            default                          => 'bg-secondary',
        };
    }

    /**
     * Kembalikan badge Bootstrap sesuai status pembayaran.
     */
    public function paymentBadgeClass(): string
    {
        return match ($this->payment_status) {
            self::PAYMENT_PAID   => 'bg-success',
            self::PAYMENT_FAILED => 'bg-danger',
            default              => 'bg-warning text-dark',
        };
    }

    /**
     * Format total_amount sebagai Rupiah.
     */
    public function formattedTotal(): string
    {
        return 'Rp ' . number_format($this->total_amount, 0, ',', '.');
    }

    /**
     * Format subtotal sebagai Rupiah.
     */
    public function formattedSubtotal(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
