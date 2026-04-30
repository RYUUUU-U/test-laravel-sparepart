<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ImageService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    /**
     * Tampilkan daftar pesanan e-commerce.
     */
    public function index(Request $request)
    {
        $query = Order::query()->orderByDesc('created_at');

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(15)->withQueryString();

        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Detail satu pesanan.
     */
    public function show(int $id)
    {
        $order = Order::with('items')->findOrFail($id);

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update status pesanan secara manual.
     */
    public function updateStatus(Request $request, int $id)
    {
        $request->validate([
            'status' => 'required|in:' . implode(',', Order::validStatuses()),
        ]);

        $order = Order::findOrFail($id);
        $order->status = $request->status;
        $order->save();

        return back()->with('success', "Status pesanan {$order->order_number} berhasil diupdate menjadi {$order->statusLabel()}.");
    }

    /**
     * Progresikan status ke tahap berikutnya dalam alur 5-tier.
     *
     * PATCH /orders/{id}/advance
     */
    public function advanceStatus(int $id)
    {
        $order = Order::findOrFail($id);
        $nextStatus = $order->nextStatus();

        if (! $nextStatus) {
            return back()->with('error', "Pesanan {$order->order_number} tidak bisa dilanjutkan dari status '{$order->statusLabel()}'.");
        }

        $data = ['status' => $nextStatus];

        // Catat timestamp sesuai tahap
        if ($nextStatus === Order::STATUS_DIPROSES) {
            $data['approved_at'] = now();
            $data['approved_by'] = session('id_user');
        } elseif ($nextStatus === Order::STATUS_DIKIRIM) {
            $data['shipped_at'] = now();
        } elseif ($nextStatus === Order::STATUS_SUDAH_TIBA) {
            $data['arrived_at'] = now();
        }

        $order->update($data);

        return back()->with('success', "Pesanan {$order->order_number} berhasil diubah ke status: {$order->statusLabel()}.");
    }

    /**
     * Setujui pesanan — ubah status ke 'disetujui' dan catat siapa yang approve.
     *
     * Alur: pesanan_disiapkan → disetujui
     * Hanya bisa disetujui jika status saat ini 'pesanan_disiapkan'.
     *
     * PATCH /orders/{id}/approve
     */
    public function approve(int $id)
    {
        $order = Order::findOrFail($id);

        // Guard: hanya bisa approve dari status pesanan_disiapkan
        if ($order->status !== Order::STATUS_PESANAN_DISIAPKAN) {
            return back()->with('error', "Pesanan {$order->order_number} tidak bisa disetujui dari status '{$order->statusLabel()}'.");
        }

        $order->update([
            'status'      => Order::STATUS_DISETUJUI,
            'approved_at' => now(),
            'approved_by' => session('id_user'),
        ]);

        return back()->with('success', "Pesanan {$order->order_number} berhasil disetujui.");
    }

    /**
     * Upload foto serah terima barang dan selesaikan pesanan.
     *
     * Alur: disetujui / barang_akan_dikirim → selesai
     * Mengisi kolom handover_photo_url dan mengubah status ke 'selesai'.
     *
     * POST /orders/{id}/handover
     */
    public function uploadHandover(Request $request, int $id)
    {
        $request->validate([
            'handover_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'handover_photo.required' => 'Foto serah terima wajib diunggah.',
            'handover_photo.image'    => 'File harus berupa gambar.',
            'handover_photo.mimes'    => 'Format gambar harus JPG, PNG, atau WebP.',
            'handover_photo.max'      => 'Ukuran foto maksimal 5MB.',
        ]);

        $order = Order::findOrFail($id);

        // Guard: hanya bisa handover dari status disetujui atau sedang_dikirim
        $allowedStatuses = [Order::STATUS_DISETUJUI, Order::STATUS_SEDANG_DIKIRIM];
        if (! in_array($order->status, $allowedStatuses)) {
            return back()->with('error', "Pesanan {$order->order_number} tidak bisa diselesaikan dari status '{$order->statusLabel()}'.");
        }

        // Simpan foto serah terima via ImageService (auto-resize 1920×1080 JPG)
        $imageService = app(ImageService::class);
        $path = $imageService->uploadHandoverPhoto($request->file('handover_photo'));

        $order->update([
            'status'             => Order::STATUS_SELESAI,
            'handover_photo_url' => $path,
        ]);

        return back()->with('success', "Pesanan {$order->order_number} telah diselesaikan. Foto serah terima berhasil diunggah.");
    }

    /**
     * Proses pengiriman barang (Admin mengunggah foto packing).
     *
     * Alur: pesanan_disiapkan -> sedang_dikirim
     * Mengisi kolom ready_to_ship_photo_url.
     *
     * POST /orders/{id}/ship
     */
    public function ship(Request $request, int $id)
    {
        $request->validate([
            'ready_to_ship_photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ], [
            'ready_to_ship_photo.required' => 'Foto barang siap kirim wajib diunggah.',
            'ready_to_ship_photo.image'    => 'File harus berupa gambar.',
            'ready_to_ship_photo.mimes'    => 'Format gambar harus JPG, PNG, atau WebP.',
            'ready_to_ship_photo.max'      => 'Ukuran foto maksimal 5MB.',
        ]);

        $order = Order::findOrFail($id);

        if ($order->status !== Order::STATUS_PESANAN_DISIAPKAN) {
            return back()->with('error', "Pesanan {$order->order_number} tidak bisa dikirim dari status '{$order->statusLabel()}'.");
        }

        $imageService = app(ImageService::class);
        // Kita menggunakan metode uploadHandoverPhoto karena fungsionalitasnya sama (kompresi & simpan logistik)
        $path = $imageService->uploadHandoverPhoto($request->file('ready_to_ship_photo'));

        $order->update([
            'status'                  => Order::STATUS_SEDANG_DIKIRIM,
            'ready_to_ship_photo_url' => $path,
        ]);

        return back()->with('success', "Pesanan {$order->order_number} berhasil dikirim. Foto packing tersimpan.");
    }
}
