<?php

namespace App\Jobs;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Job queue untuk memproses webhook Xendit secara asinkron.
 *
 * Saat status PAID:
 *  1. Update status order → pesanan_disiapkan
 *  2. Decrement stok barang (dengan lockForUpdate untuk thread-safety)
 *  3. Catat mutasi ke tabel barang_keluar
 *
 * Saat status EXPIRED/FAILED:
 *  - Update status order → gagal / dibatalkan
 */
class ProcessXenditWebhook implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Jumlah percobaan ulang jika job gagal.
     */
    public int $tries = 3;

    /**
     * Delay antar retry (detik).
     */
    public int $backoff = 10;

    public function __construct(
        private array $payload
    ) {}

    public function handle(): void
    {
        $externalId = $this->payload['external_id'] ?? null;
        $status     = $this->payload['status'] ?? null;

        if (! $externalId || ! $status) {
            Log::error('ProcessXenditWebhook: missing external_id or status', $this->payload);
            return;
        }

        // Cari order berdasarkan xendit_external_id
        $order = Order::where('xendit_external_id', $externalId)->first();

        if (! $order) {
            Log::error('ProcessXenditWebhook: order not found', ['external_id' => $externalId]);
            return;
        }

        // Cegah double-processing
        if ($order->payment_status === Order::PAYMENT_PAID) {
            Log::info('ProcessXenditWebhook: order already paid, skipping', [
                'order_number' => $order->order_number,
            ]);
            return;
        }

        match ($status) {
            'PAID'    => $this->handlePaid($order),
            'EXPIRED' => $this->handleExpiredOrFailed($order, Order::STATUS_DIBATALKAN),
            'FAILED'  => $this->handleExpiredOrFailed($order, Order::STATUS_GAGAL),
            default   => Log::info("ProcessXenditWebhook: unhandled status '{$status}'", [
                'order_number' => $order->order_number,
            ]),
        };
    }

    /**
     * Proses pembayaran berhasil (PAID).
     *
     * Menggunakan DB::transaction + lockForUpdate untuk thread-safety
     * saat mengurangi stok barang.
     */
    private function handlePaid(Order $order): void
    {
        DB::transaction(function () use ($order) {

            // Load order items
            $items = $order->items()->with('barang')->get();

            // Sort berdasarkan barang_id untuk mencegah deadlock
            $sortedItems = $items->sortBy('barang_id');

            foreach ($sortedItems as $item) {
                // Lock baris barang untuk update
                $barang = Barang::where('id_barang', $item->barang_id)
                    ->lockForUpdate()
                    ->first();

                if (! $barang) {
                    Log::error('ProcessXenditWebhook: barang not found', [
                        'barang_id'    => $item->barang_id,
                        'order_number' => $order->order_number,
                    ]);
                    continue;
                }

                // Decrement stok
                $barang->decrement('stok', $item->quantity);

                // Catat mutasi ke barang_keluar
                BarangKeluar::create([
                    'id_barang'      => $item->barang_id,
                    'tanggal_keluar' => now(),
                    'jumlah_keluar'  => $item->quantity,
                    'total_harga'    => $item->subtotal,
                ]);
            }

            // Update order status
            $trackingNumber = 'RESI-' . now()->format('Ymd') . '-' . strtoupper(\Illuminate\Support\Str::random(4));

            $order->update([
                'status'                 => Order::STATUS_PESANAN_DISIAPKAN,
                'payment_status'         => Order::PAYMENT_PAID,
                'tracking_number'        => $trackingNumber,
                'paid_at'                => now(),
                'xendit_payment_channel' => $this->payload['payment_channel'] ?? null,
                'xendit_payment_method'  => $this->payload['payment_method'] ?? null,
            ]);
        });

        Log::info('ProcessXenditWebhook: payment processed successfully', [
            'order_number' => $order->order_number,
            'amount'       => $order->total_amount,
        ]);
    }

    /**
     * Proses pembayaran gagal atau expired.
     */
    private function handleExpiredOrFailed(Order $order, string $newStatus): void
    {
        $order->update([
            'status'         => $newStatus,
            'payment_status' => Order::PAYMENT_FAILED,
        ]);

        Log::info("ProcessXenditWebhook: order marked as {$newStatus}", [
            'order_number' => $order->order_number,
        ]);
    }
}
