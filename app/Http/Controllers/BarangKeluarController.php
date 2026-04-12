<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangKeluar;
use App\Services\PenjualanService;
use Illuminate\Http\Request;

class BarangKeluarController extends Controller
{
    public function __construct(private PenjualanService $penjualanService) {}

    /**
     * Daftar barang keluar untuk admin & owner.
     * Native: barang_keluar.php
     */
    public function index(Request $request)
    {
        $limit      = $request->input('limit', 10);
        $tglMulai   = $request->input('tgl_mulai', '');
        $tglSelesai = $request->input('tgl_selesai', '');

        $query = BarangKeluar::with('barang')->orderByDesc('tanggal_keluar');

        if ($tglMulai && $tglSelesai) {
            $query->whereDate('tanggal_keluar', '>=', $tglMulai)
                  ->whereDate('tanggal_keluar', '<=', $tglSelesai);
        }

        $dataKeluar = $query->paginate($limit)->withQueryString();

        return view('barang_keluar.index', compact('dataKeluar', 'limit', 'tglMulai', 'tglSelesai'));
    }

    /**
     * Riwayat transaksi untuk kasir + filter/pagination.
     * Native: tambah_barang_keluar.php
     */
    public function kasir(Request $request)
    {
        $limit      = $request->input('limit', 10);
        $tglMulai   = $request->input('tgl_mulai', '');
        $tglSelesai = $request->input('tgl_selesai', '');

        $query = BarangKeluar::with('barang')->orderByDesc('tanggal_keluar');

        if ($tglMulai && $tglSelesai) {
            $query->whereDate('tanggal_keluar', '>=', $tglMulai)
                  ->whereDate('tanggal_keluar', '<=', $tglSelesai);
        }

        $dataKeluar = $query->paginate($limit)->withQueryString();

        return view('barang_keluar.kasir', compact('dataKeluar', 'limit', 'tglMulai', 'tglSelesai'));
    }

    /**
     * Form input penjualan baru. Native: form_penjualan.php (GET)
     */
    public function create()
    {
        $barang = Barang::where('stok', '>', 0)->orderBy('nama_barang')->get();
        return view('barang_keluar.create', compact('barang'));
    }

    /**
     * Simpan transaksi penjualan. Native: form_penjualan.php (POST)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_barang' => 'required|exists:barang,id_barang',
            'jumlah'    => 'required|integer|min:1',
            'tanggal'   => 'required|date',
        ]);

        $waktuLengkap = $request->tanggal . ' ' . now()->format('H:i:s');

        $result = $this->penjualanService->simpanTransaksi(
            $request->id_barang,
            $request->jumlah,
            $waktuLengkap
        );

        if (!$result['success']) {
            return back()->with('error', $result['message']);
        }

        return redirect()->route('barang-keluar.kasir')
            ->with('success', $result['message']);
    }
}
