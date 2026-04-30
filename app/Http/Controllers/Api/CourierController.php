<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\ImageService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CourierController extends Controller
{
    /**
     * Endpoint simulasi aplikasi kurir pihak ketiga untuk mengunggah bukti barang sampai.
     * 
     * POST /api/courier/delivered/{order_number}
     */
    public function delivered(Request $request, string $orderNumber)
    {
        // Karena ini endpoint API public, log setiap request yang masuk
        Log::info("Courier API Webhook received for order: {$orderNumber}");

        $request->validate([
            'photo' => 'required|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $order = Order::where('order_number', $orderNumber)->first();

        if (! $order) {
            return response()->json([
                'success' => false,
                'message' => 'Order number not found.'
            ], 404);
        }

        // Memastikan status pesanan sudah dikirim oleh Admin sebelumnya
        if ($order->status !== Order::STATUS_SEDANG_DIKIRIM) {
            return response()->json([
                'success' => false,
                'message' => "Order is not in status '" . Order::STATUS_SEDANG_DIKIRIM . "'. Current status: " . $order->status
            ], 400);
        }

        try {
            $imageService = app(ImageService::class);
            $path = $imageService->uploadHandoverPhoto($request->file('photo'));

            $order->update([
                'status'             => Order::STATUS_SELESAI,
                'handover_photo_url' => $path,
                'shipped_at'         => now(),
            ]);

            Log::info("Courier API successful. Order {$orderNumber} marked as SELESAI.");

            return response()->json([
                'success' => true,
                'message' => 'Delivery proof uploaded successfully. Order is now completed.',
                'data'    => [
                    'order_number' => $order->order_number,
                    'status'       => $order->status,
                    'shipped_at'   => $order->shipped_at,
                ]
            ]);

        } catch (\Exception $e) {
            Log::error("Courier API failed for order {$orderNumber}: " . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Failed to process delivery proof: ' . $e->getMessage()
            ], 500);
        }
    }
}
