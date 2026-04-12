<?php

namespace App\Http\Controllers;

use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
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
