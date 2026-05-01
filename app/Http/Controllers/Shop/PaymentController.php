<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    /**
     * Tampilkan halaman invoice (bisa dicetak).
     * GET /invoice/{token}
     */
    public function invoice(string $token)
    {
        $orderNumber = $token;
        
        try {
            // Coba decrypt jika itu token terenkripsi
            $orderNumber = decrypt($token);
        } catch (\Exception $e) {
            // Jika gagal decrypt, asumsikan itu raw order number (ORD-...)
            if (!str_starts_with($token, 'ORD-')) {
                abort(404, 'Tautan tidak valid.');
            }
        }

        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return view('shop.invoice', compact('order'));
    }

    /**
     * Download invoice sebagai PDF.
     * GET /invoice/{token}/pdf
     */
    public function downloadPdf(string $token)
    {
        $orderNumber = $token;

        try {
            // Coba decrypt jika itu token terenkripsi
            $orderNumber = decrypt($token);
        } catch (\Exception $e) {
            // Jika gagal decrypt, asumsikan itu raw order number (ORD-...)
            if (!str_starts_with($token, 'ORD-')) {
                abort(404, 'Tautan tidak valid.');
            }
        }

        $order = Order::with('items')
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $pdf = Pdf::loadView('shop.invoice_pdf', compact('order'));

        return $pdf->download("Invoice-{$order->order_number}.pdf");
    }
}
