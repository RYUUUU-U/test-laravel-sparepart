<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class CustomerOrderController extends Controller
{
    /**
     * Menampilkan daftar riwayat pesanan milik pelanggan yang sedang login.
     */
    public function index(Request $request)
    {
        $customerId = session('customer_id');

        if (! $customerId) {
            return redirect()->route('customer.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $orders = Order::where('customer_id', $customerId)
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Cek kedaluwarsa untuk semua pesanan di halaman ini
        $orders->getCollection()->each->checkExpiration();

        return view('shop.customer.orders.index', compact('orders'));
    }

    /**
     * Menampilkan detail spesifik dari satu pesanan pelanggan (Tracking & Invoice).
     */
    public function show(string $orderNumber)
    {
        $customerId = session('customer_id');

        if (! $customerId) {
            return redirect()->route('customer.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->where('customer_id', $customerId)
            ->firstOrFail();

        $order->checkExpiration();

        return view('shop.customer.orders.show', compact('order'));
    }

    /**
     * Menyimpan rating dan review untuk item pesanan yang sudah selesai.
     */
    public function storeReview(Request $request, \App\Models\OrderItem $orderItem)
    {
        $customerId = session('customer_id');

        if (! $customerId) {
            return redirect()->route('customer.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Pastikan order item ini milik customer yang login
        if ($orderItem->order->customer_id !== $customerId) {
            abort(403, 'Akses ditolak.');
        }

        // Pastikan status order adalah sudah_tiba atau selesai
        if (! in_array($orderItem->order->status, [Order::STATUS_SUDAH_TIBA, Order::STATUS_SELESAI])) {
            return back()->with('error', 'Anda hanya dapat memberikan penilaian untuk pesanan yang sudah tiba atau selesai.');
        }

        // Pastikan belum pernah direview
        if ($orderItem->rating) {
            return back()->with('error', 'Anda sudah memberikan penilaian untuk produk ini.');
        }

        $validated = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:1000',
        ]);

        $orderItem->update([
            'rating' => $validated['rating'],
            'review' => $validated['review']
        ]);

        return back()->with('success', 'Terima kasih atas penilaian Anda!');
    }
}
