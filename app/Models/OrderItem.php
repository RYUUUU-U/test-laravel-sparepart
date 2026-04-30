<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $table      = 'order_items';
    protected $primaryKey = 'id';
    public    $timestamps  = true;

    protected $fillable = [
        'order_id',
        'barang_id',
        'product_name',
        'product_code',
        'price',
        'quantity',
        'subtotal',
        'rating',
        'review',
    ];

    protected $casts = [
        'price'    => 'integer',
        'quantity' => 'integer',
        'subtotal' => 'integer',
    ];

    // ── Relations ─────────────────────────────────────────────────────────────

    /**
     * Pesanan induk dari item ini.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /**
     * Data barang dari katalog (menggunakan barang_id → barang.id_barang).
     */
    public function barang(): BelongsTo
    {
        return $this->belongsTo(Barang::class, 'barang_id', 'id_barang');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    /**
     * Format harga satuan sebagai Rupiah.
     */
    public function formattedPrice(): string
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    /**
     * Format subtotal sebagai Rupiah.
     */
    public function formattedSubtotal(): string
    {
        return 'Rp ' . number_format($this->subtotal, 0, ',', '.');
    }
}
