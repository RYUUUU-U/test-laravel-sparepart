<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LaporanController extends Controller
{
    /**
     * Laporan Keuangan — Revenue dari pesanan yang sudah dibayar.
     */
    public function keuangan(Request $request)
    {
        $tglMulai   = $request->input('tgl_mulai', '');
        $tglSelesai = $request->input('tgl_selesai', '');

        // Query orders yang payment_status = paid (mencakup semua status mulai dibayar hingga selesai)
        $query = Order::where('payment_status', Order::PAYMENT_PAID);

        if ($tglMulai && $tglSelesai) {
            $query->whereDate('paid_at', '>=', $tglMulai)
                  ->whereDate('paid_at', '<=', $tglSelesai);
        }

        $orders     = $query->orderByDesc('paid_at')->get();
        $totalRevenue = $orders->sum('total_amount');
        $orderCount   = $orders->count();
        $avgOrder     = $orderCount > 0 ? round($totalRevenue / $orderCount) : 0;

        // Revenue dari penjualan offline (barang_keluar)
        $offlineQuery = BarangKeluar::query();
        if ($tglMulai && $tglSelesai) {
            $offlineQuery->whereDate('tanggal_keluar', '>=', $tglMulai)
                         ->whereDate('tanggal_keluar', '<=', $tglSelesai);
        }
        $offlineRevenue = $offlineQuery->sum('total_harga');
        $offlineCount   = $offlineQuery->count();

        $grandTotal = $totalRevenue + $offlineRevenue;

        $labelWaktu = ($tglMulai && $tglSelesai)
            ? 'Periode: ' . \Carbon\Carbon::parse($tglMulai)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($tglSelesai)->format('d M Y')
            : 'Semua Waktu';

        return view('laporan.keuangan', compact(
            'orders', 'totalRevenue', 'orderCount', 'avgOrder',
            'offlineRevenue', 'offlineCount', 'grandTotal',
            'tglMulai', 'tglSelesai', 'labelWaktu'
        ));
    }

    /**
     * Laporan Penjualan — Pergerakan barang terjual.
     */
    public function penjualan(Request $request)
    {
        $tglMulai   = $request->input('tgl_mulai', '');
        $tglSelesai = $request->input('tgl_selesai', '');

        // === Data dari pesanan online (order_items) ===
        $onlineQuery = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.payment_status', 'paid')
            ->select(
                'order_items.barang_id',
                'order_items.product_name',
                'order_items.product_code',
                DB::raw('SUM(order_items.quantity) as qty_online'),
                DB::raw('SUM(order_items.subtotal) as revenue_online')
            );

        if ($tglMulai && $tglSelesai) {
            $onlineQuery->whereDate('orders.paid_at', '>=', $tglMulai)
                        ->whereDate('orders.paid_at', '<=', $tglSelesai);
        }

        $onlineData = $onlineQuery->groupBy('order_items.barang_id', 'order_items.product_name', 'order_items.product_code')
                                   ->get()
                                   ->keyBy('barang_id');

        // === Data dari penjualan offline (barang_keluar) ===
        $offlineQuery = DB::table('barang_keluar')
            ->join('barang', 'barang_keluar.id_barang', '=', 'barang.id_barang')
            ->select(
                'barang_keluar.id_barang as barang_id',
                'barang.nama_barang as product_name',
                'barang.kode_barang as product_code',
                DB::raw('SUM(barang_keluar.jumlah_keluar) as qty_offline'),
                DB::raw('SUM(barang_keluar.total_harga) as revenue_offline')
            );

        if ($tglMulai && $tglSelesai) {
            $offlineQuery->whereDate('barang_keluar.tanggal_keluar', '>=', $tglMulai)
                         ->whereDate('barang_keluar.tanggal_keluar', '<=', $tglSelesai);
        }

        $offlineData = $offlineQuery->groupBy('barang_keluar.id_barang', 'barang.nama_barang', 'barang.kode_barang')
                                     ->get()
                                     ->keyBy('barang_id');

        // === Merge kedua sumber data ===
        $allIds = $onlineData->keys()->merge($offlineData->keys())->unique();

        $salesData = $allIds->map(function ($id) use ($onlineData, $offlineData) {
            $online  = $onlineData->get($id);
            $offline = $offlineData->get($id);

            $qtyOnline   = $online->qty_online ?? 0;
            $qtyOffline  = $offline->qty_offline ?? 0;
            $revOnline   = $online->revenue_online ?? 0;
            $revOffline  = $offline->revenue_offline ?? 0;

            return (object) [
                'barang_id'    => $id,
                'product_name' => $online->product_name ?? $offline->product_name ?? '-',
                'product_code' => $online->product_code ?? $offline->product_code ?? '-',
                'qty_online'   => $qtyOnline,
                'qty_offline'  => $qtyOffline,
                'qty_total'    => $qtyOnline + $qtyOffline,
                'revenue_online'  => $revOnline,
                'revenue_offline' => $revOffline,
                'revenue_total'   => $revOnline + $revOffline,
            ];
        })->sortByDesc('qty_total')->values();

        $totalItemsSold  = $salesData->sum('qty_total');
        $totalRevenue    = $salesData->sum('revenue_total');

        $labelWaktu = ($tglMulai && $tglSelesai)
            ? 'Periode: ' . \Carbon\Carbon::parse($tglMulai)->format('d M Y') . ' s/d ' . \Carbon\Carbon::parse($tglSelesai)->format('d M Y')
            : 'Semua Waktu';

        return view('laporan.penjualan', compact(
            'salesData', 'totalItemsSold', 'totalRevenue',
            'tglMulai', 'tglSelesai', 'labelWaktu'
        ));
    }

    /**
     * Export Excel Barang Keluar.
     * Native: export_excel.php
     * Menggunakan output HTML→XLS manual (tidak butuh package) — ringan dan efisien.
     */
    public function exportExcelKeluar(Request $request)
    {
        $tglMulai   = $request->input('tgl_mulai', '');
        $tglSelesai = $request->input('tgl_selesai', '');

        $query = BarangKeluar::with('barang')->orderByDesc('tanggal_keluar');
        if ($tglMulai && $tglSelesai) {
            $query->whereDate('tanggal_keluar', '>=', $tglMulai)
                  ->whereDate('tanggal_keluar', '<=', $tglSelesai);
        }
        $data       = $query->get();
        $grandTotal = $data->sum('total_harga');
        $labelWaktu = ($tglMulai && $tglSelesai) ? "Periode: $tglMulai s/d $tglSelesai" : 'Semua Waktu';

        return response()->streamDownload(function () use ($data, $grandTotal, $labelWaktu) {
            echo view('laporan.export_keluar', compact('data', 'grandTotal', 'labelWaktu'))->render();
        }, 'Laporan_Penjualan.xls', [
            'Content-Type' => 'application/vnd-ms-excel',
        ]);
    }

    /**
     * Export Excel Barang Masuk.
     * Native: export_excel_masuk.php
     */
    public function exportExcelMasuk(Request $request)
    {
        $tglMulai   = $request->input('tgl_mulai', '');
        $tglSelesai = $request->input('tgl_selesai', '');

        $query = BarangMasuk::with(['barang', 'supplier'])->orderByDesc('tanggal_masuk');
        if ($tglMulai && $tglSelesai) {
            $query->whereDate('tanggal_masuk', '>=', $tglMulai)
                  ->whereDate('tanggal_masuk', '<=', $tglSelesai);
        }
        $data       = $query->get();
        $totalMasuk = $data->sum('jumlah_masuk');
        $labelWaktu = ($tglMulai && $tglSelesai) ? "Periode: $tglMulai s/d $tglSelesai" : 'Semua Waktu';

        return response()->streamDownload(function () use ($data, $totalMasuk, $labelWaktu) {
            echo view('laporan.export_masuk', compact('data', 'totalMasuk', 'labelWaktu'))->render();
        }, 'Laporan_Barang_Masuk.xls', [
            'Content-Type' => 'application/vnd-ms-excel',
        ]);
    }

    /**
     * Cetak / Print laporan barang keluar.
     * Native: cetak_laporan.php
     */
    public function cetakKeluar(Request $request)
    {
        $tglMulai   = $request->input('tgl_mulai', '');
        $tglSelesai = $request->input('tgl_selesai', '');

        $query = BarangKeluar::with('barang')->orderByDesc('tanggal_keluar');
        if ($tglMulai && $tglSelesai) {
            $query->whereDate('tanggal_keluar', '>=', $tglMulai)
                  ->whereDate('tanggal_keluar', '<=', $tglSelesai);
        }
        $data       = $query->get();
        $grandTotal = $data->sum('total_harga');
        $label = ($tglMulai && $tglSelesai)
            ? 'Periode: ' . \Carbon\Carbon::parse($tglMulai)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($tglSelesai)->format('d-m-Y')
            : 'Semua Data';

        return view('laporan.cetak_keluar', compact('data', 'grandTotal', 'label'));
    }

    /**
     * Cetak / Print laporan barang masuk.
     * Native: cetak_masuk.php
     */
    public function cetakMasuk(Request $request)
    {
        $tglMulai   = $request->input('tgl_mulai', '');
        $tglSelesai = $request->input('tgl_selesai', '');

        $query = BarangMasuk::with(['barang', 'supplier'])->orderByDesc('tanggal_masuk');
        if ($tglMulai && $tglSelesai) {
            $query->whereDate('tanggal_masuk', '>=', $tglMulai)
                  ->whereDate('tanggal_masuk', '<=', $tglSelesai);
        }
        $data       = $query->get();
        $totalMasuk = $data->sum('jumlah_masuk');
        $label = ($tglMulai && $tglSelesai)
            ? 'Periode: ' . \Carbon\Carbon::parse($tglMulai)->format('d-m-Y') . ' s/d ' . \Carbon\Carbon::parse($tglSelesai)->format('d-m-Y')
            : 'Semua Data';

        return view('laporan.cetak_masuk', compact('data', 'totalMasuk', 'label'));
    }
}
