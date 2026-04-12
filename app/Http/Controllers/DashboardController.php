<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Models\BarangMasuk;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * Dashboard Admin.
     * Native: dashboard_admin.php
     */
    public function admin()
    {
        $today = now()->toDateString();

        $jmlBarang    = Barang::count();
        $jmlSupplier  = Supplier::count();
        $jmlUser      = User::count();
        $jmlTransaksi = BarangKeluar::whereDate('tanggal_keluar', $today)->count();

        // Data grafik terlaris (Top 10)
        $terlaris = DB::table('barang_keluar')
            ->join('barang', 'barang_keluar.id_barang', '=', 'barang.id_barang')
            ->select('barang.nama_barang', DB::raw('SUM(barang_keluar.jumlah_keluar) as total'))
            ->groupBy('barang_keluar.id_barang', 'barang.nama_barang')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        // Data grafik stok terbanyak (Top 10)
        $stokTerbanyak = Barang::select('nama_barang', 'stok')
            ->orderByDesc('stok')
            ->limit(10)
            ->get();

        // Stok menipis
        $stokMinim = Barang::where('stok', '<=', 5)->get();

        return view('dashboard.admin', compact(
            'jmlBarang', 'jmlSupplier', 'jmlUser', 'jmlTransaksi',
            'terlaris', 'stokTerbanyak', 'stokMinim'
        ));
    }

    /**
     * Dashboard Kasir.
     * Native: dashboard_kasir.php
     */
    public function kasir()
    {
        $today = now()->toDateString();

        $jmlBarang          = Barang::count();
        $jmlTransaksiMasuk  = BarangMasuk::whereDate('tanggal_masuk', $today)->count();
        $jmlTransaksiKeluar = BarangKeluar::whereDate('tanggal_keluar', $today)->count();
        $stokMinim          = Barang::where('stok', '<=', 5)->get();

        return view('dashboard.kasir', compact(
            'jmlBarang', 'jmlTransaksiMasuk', 'jmlTransaksiKeluar', 'stokMinim'
        ));
    }

    /**
     * Dashboard Owner.
     * Native: dashboard_owner.php
     */
    public function owner()
    {
        $jmlBarang    = Barang::count();
        $jmlTransaksi = BarangKeluar::count();

        $terlaris = DB::table('barang_keluar')
            ->join('barang', 'barang_keluar.id_barang', '=', 'barang.id_barang')
            ->select('barang.nama_barang', DB::raw('SUM(barang_keluar.jumlah_keluar) as total'))
            ->groupBy('barang_keluar.id_barang', 'barang.nama_barang')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        return view('dashboard.owner', compact('jmlBarang', 'jmlTransaksi', 'terlaris'));
    }
}
