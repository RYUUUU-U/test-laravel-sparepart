<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    /**
     * Tampilkan form checkout beserta ringkasan pesanan dari cart.
     * GET /checkout
     */
    public function index()
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Keranjang Anda masih kosong.');
        }

        $subtotal     = collect($cart)->sum('subtotal');
        $shippingCost = 0;       // Gratis ongkir untuk dummy flow
        $total        = $subtotal + $shippingCost;

        // Prefill data dari session customer
        $customerData = [
            'name'    => session('customer_name', ''),
            'email'   => session('customer_email', ''),
            'phone'   => '',
            'address' => '',
        ];

        if (session()->has('customer_id')) {
            $customerModel = \App\Models\Customer::find(session('customer_id'));
            if ($customerModel) {
                $customerData['phone'] = $customerModel->phone;
                $customerData['address'] = trim(implode(', ', array_filter([
                    $customerModel->address,
                    $customerModel->city,
                    $customerModel->province,
                    $customerModel->postal_code
                ])));
            }
        }

        return view('shop.checkout', [
            'cart'         => $cart,
            'subtotal'     => $subtotal,
            'shippingCost' => $shippingCost,
            'total'        => $total,
            'customer'     => $customerData
        ]);
    }

    /**
     * Proses checkout dengan Pessimistic Locking (thread-safe).
     *
     * Alur:
     *  1. Validasi form
     *  2. Validasi keranjang tidak kosong
     *  3. DB::transaction() dengan lockForUpdate():
     *     a. Sort item berdasarkan ID → cegah deadlock
     *     b. Kunci stok (lockForUpdate) untuk setiap item
     *     c. Validasi ketersediaan stok
     *     d. Buat Order (status: awaiting_payment)
     *     e. Buat OrderItems
     *     f. STOK BELUM DIKURANGI — akan dikurangi saat pembayaran dikonfirmasi
     *  4. Buat invoice Xendit
     *  5. Hapus cart dari session
     *  6. Redirect ke halaman pembayaran Xendit (atau invoice lokal sebagai fallback)
     *
     * POST /checkout
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'    => 'required|string|max:100',
            'email'   => 'required|email|max:150',
            'phone'   => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'notes'   => 'nullable|string|max:500',
        ], [
            'name.required'    => 'Nama penerima wajib diisi.',
            'email.required'   => 'Email wajib diisi.',
            'phone.required'   => 'Nomor telepon wajib diisi.',
            'address.required' => 'Alamat pengiriman wajib diisi.',
        ]);

        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect('/cart')->with('error', 'Keranjang Anda kosong. Silakan tambahkan produk terlebih dahulu.');
        }

        // ── Bungkus semua proses dalam try-catch ────────────────────────────
        DB::beginTransaction();

        try {
            // ── a. Sort item berdasarkan ID untuk mencegah deadlock ────────
            $sortedCart = collect($cart)->sortKeys()->all();

            // ── b. Validasi & kunci stok semua item (lockForUpdate) ───────
            foreach ($sortedCart as $id => $item) {
                $barang = Barang::where('id_barang', $id)
                    ->lockForUpdate()
                    ->first();

                if (! $barang) {
                    throw new \Exception("Produk '{$item['name']}' tidak ditemukan di database.");
                }

                if ($barang->stok < $item['quantity']) {
                    throw new \Exception(
                        "Stok {$barang->nama_barang} tidak mencukupi. " .
                        "Diminta: {$item['quantity']}, Tersedia: {$barang->stok}."
                    );
                }
            }

            // ── c. Hitung total ───────────────────────────────────────────
            $subtotal     = collect($sortedCart)->sum('subtotal');
            $shippingCost = 0;
            $totalAmount  = $subtotal + $shippingCost;
            $orderNumber  = $this->generateOrderNumber();

            // ── d. Buat Order (awaiting_payment — belum dibayar) ──────────
            $order = Order::create([
                'order_number'     => $orderNumber,
                'customer_id'      => session('customer_id'),
                'customer_name'    => $request->name,
                'customer_email'   => $request->email,
                'customer_phone'   => $request->phone,
                'customer_address' => $request->address,
                'subtotal'         => $subtotal,
                'shipping_cost'    => $shippingCost,
                'total_amount'     => $totalAmount,
                'status'           => Order::STATUS_AWAITING_PAYMENT,
                'payment_status'   => Order::PAYMENT_UNPAID,
                'payment_method'   => 'xendit',
                'notes'            => $request->notes,
            ]);

            // ── e. Buat OrderItems (stok BELUM dikurangi) ────────────────
            foreach ($sortedCart as $id => $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'barang_id'    => $item['id'],
                    'product_name' => $item['name'],
                    'product_code' => $item['code'],
                    'price'        => $item['price'],
                    'quantity'     => $item['quantity'],
                    'subtotal'     => $item['subtotal'],
                ]);
            }

            // ── f. Buat invoice Xendit ────────────────────────────────────
            $xenditService = app(XenditService::class);

            $invoiceResult = $xenditService->createInvoice([
                'order_number'  => $order->order_number,
                'amount'        => $order->total_amount,
                'payer_email'   => $order->customer_email,
                'description'   => "Pembayaran pesanan {$order->order_number}",
                'customer_name' => $order->customer_name,
                'items'         => $order->items->map(fn($item) => [
                    'name'     => $item->product_name,
                    'quantity' => $item->quantity,
                    'price'    => $item->price,
                ])->toArray(),
            ]);

            if ($invoiceResult['success']) {
                $order->update([
                    'xendit_external_id' => $invoiceResult['external_id'],
                    'xendit_invoice_id'  => $invoiceResult['invoice_id'],
                    'xendit_invoice_url' => $invoiceResult['invoice_url'],
                ]);

                DB::commit();

                // Kosongkan cart setelah semua berhasil
                session()->forget('cart');

                // Redirect ke Xendit Invoice URL (dashboard pembayaran Xendit)
                return redirect()->away($invoiceResult['invoice_url']);
            }

            // Jika Xendit gagal, tetap commit order (bisa retry nanti)
            DB::commit();
            session()->forget('cart');

            // Fallback: redirect ke invoice lokal (dimana ada tombol simulasi)
            return redirect()->route('shop.invoice', $order->order_number)
                ->with('warning', 'Invoice Xendit gagal dibuat, gunakan simulasi pembayaran atau coba lagi nanti.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Checkout failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            return back()
                ->withInput()
                ->with('error', 'Gagal memproses pesanan: ' . $e->getMessage());
        }
    }

    /**
     * Halaman konfirmasi sukses setelah checkout / setelah pembayaran.
     * GET /order/success/{token}
     */
    public function success(string $token, XenditService $xenditService)
    {
        try {
            $orderNumber = decrypt($token);
        } catch (\Exception $e) {
            abort(404, 'Tautan tidak valid.');
        }

        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $order->checkExpiration();

        // ── SINKRONISASI AKTIF UNTUK LOCALHOST ─────────────────────────
        // Karena webhook dari Xendit tidak bisa masuk ke "localhost", 
        // kita lakukan pengecekan status secara sinkron saat kustomer 
        // kembali ke halaman ini dari Xendit.
        if ($order->payment_status !== Order::PAYMENT_PAID && $order->xendit_invoice_id) {
            $invoice = $xenditService->getInvoice($order->xendit_invoice_id);

            if ($invoice && in_array($invoice['status'], ['PAID', 'SETTLED'])) {
                // Jalankan proses yang sama seperti Webhook secara sinkron
                \App\Jobs\ProcessXenditWebhook::dispatchSync([
                    'external_id'     => $order->xendit_external_id,
                    'status'          => 'PAID',
                    'payment_channel' => $invoice['payment_channel'] ?? null,
                    'payment_method'  => $invoice['payment_method'] ?? null,
                ]);

                // Segarkan data dari database setelah diupdate oleh Job
                $order->refresh();

                session()->now('success', 'Pembayaran berhasil diverifikasi secara otomatis dari Xendit!');
            }
        }

        return view('shop.success', compact('order'));
    }

    /**
     * Simulasikan Pembayaran Berhasil (Dummy Mode).
     *
     * Logika:
     *  1. Validasi order dalam status awaiting_payment
     *  2. DB::transaction + lockForUpdate untuk thread-safety
     *  3. Kurangi stok barang
     *  4. Catat mutasi ke barang_keluar
     *  5. Update status order → pesanan_disiapkan, payment → paid
     *
     * POST /payment/simulate/{token}
     */
    public function simulatePayment(string $token)
    {
        try {
            $orderNumber = decrypt($token);
        } catch (\Exception $e) {
            abort(404, 'Tautan tidak valid.');
        }

        $order = Order::with('items')->where('order_number', $orderNumber)->firstOrFail();
        $order->checkExpiration();

        // Validasi: hanya order yang belum dibayar yang bisa disimulasikan
        if ($order->payment_status === Order::PAYMENT_PAID) {
            return redirect()->route('shop.order.success', encrypt($order->order_number))
                ->with('info', 'Pesanan ini sudah dibayar.');
        }

        if ($order->status !== Order::STATUS_AWAITING_PAYMENT) {
            return back()->with('error', 'Pesanan ini tidak dalam status menunggu pembayaran.');
        }

        DB::beginTransaction();

        try {
            // Sort item berdasarkan barang_id untuk mencegah deadlock
            $sortedItems = $order->items->sortBy('barang_id');

            foreach ($sortedItems as $item) {
                // Lock baris barang untuk update
                $barang = Barang::where('id_barang', $item->barang_id)
                    ->lockForUpdate()
                    ->first();

                if (! $barang) {
                    throw new \Exception("Barang dengan ID {$item->barang_id} tidak ditemukan.");
                }

                if ($barang->stok < $item->quantity) {
                    throw new \Exception(
                        "Stok {$barang->nama_barang} tidak mencukupi. " .
                        "Diminta: {$item->quantity}, Tersedia: {$barang->stok}."
                    );
                }

                // Kurangi stok
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
                'xendit_payment_channel' => 'SIMULATED_DEMO',
                'xendit_payment_method'  => 'SIMULATED_DEMO_BANK',
            ]);

            DB::commit();

            Log::info('Simulated payment processed', [
                'order_number' => $order->order_number,
                'amount'       => $order->total_amount,
            ]);

            return redirect()->route('shop.order.success', encrypt($order->order_number))
                ->with('success', '🎉 [SIMULASI DEMO] Pembayaran berhasil diproses! Stok telah dikurangi.');

        } catch (\Exception $e) {
            DB::rollBack();

            Log::error('Simulated payment failed', [
                'order_number' => $order->order_number,
                'message'      => $e->getMessage(),
            ]);

            return back()->with('error', 'Simulasi pembayaran gagal: ' . $e->getMessage());
        }
    }

    // ── Private Helpers ───────────────────────────────────────────────────────

    /**
     * Generate nomor order unik: ORD-YYYYMMDD-XXXXXX
     * Contoh: ORD-20260412-A3F9K2
     */
    private function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
        } while (Order::where('order_number', $number)->exists());

        return $number;
    }
}
