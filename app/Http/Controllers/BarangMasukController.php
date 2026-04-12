<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Models\BarangMasuk;
use App\Models\Supplier;
use Illuminate\Http\Request;

class BarangMasukController extends Controller
{
    /**
     * Daftar barang masuk dengan filter & pagination.
     * Native: barang_masuk.php
     */
    public function index(Request $request)
    {
        $limit      = $request->input('limit', 10);
        $tglMulai   = $request->input('tgl_mulai', '');
        $tglSelesai = $request->input('tgl_selesai', '');

        $query = BarangMasuk::with(['barang', 'supplier'])
            ->orderByDesc('tanggal_masuk');

        if ($tglMulai && $tglSelesai) {
            $query->whereDate('tanggal_masuk', '>=', $tglMulai)
                  ->whereDate('tanggal_masuk', '<=', $tglSelesai);
        }

        $dataMasuk = $query->paginate($limit)->withQueryString();

        return view('barang_masuk.index', compact('dataMasuk', 'limit', 'tglMulai', 'tglSelesai'));
    }

    /**
     * Form tambah barang masuk.
     * Native: tambah_barang_masuk.php (GET)
     */
    public function create()
    {
        $barang    = Barang::orderBy('nama_barang')->get();
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        return view('barang_masuk.create', compact('barang', 'suppliers'));
    }

    /**
     * Simpan barang masuk + update stok.
     * Native: tambah_barang_masuk.php (POST)
     */
    public function store(Request $request)
    {
        $request->validate([
            'id_barang'    => 'required|exists:barang,id_barang',
            'id_supplier'  => 'required|exists:supplier,id_supplier',
            'tanggal'      => 'required|date',
            'jumlah'       => 'required|integer|min:1',
        ]);

        BarangMasuk::create([
            'id_barang'    => $request->id_barang,
            'id_supplier'  => $request->id_supplier,
            'tanggal_masuk'=> $request->tanggal,
            'jumlah_masuk' => $request->jumlah,
        ]);

        // Update stok barang
        Barang::where('id_barang', $request->id_barang)
              ->increment('stok', $request->jumlah);

        $redirectRoute = session('role') === 'kasir'
            ? 'dashboard.kasir'
            : 'barang-masuk.index';

        return redirect()->route($redirectRoute)
            ->with('success', 'Stok berhasil ditambahkan!');
    }

    /**
     * Form edit barang masuk. Native: edit_barang_masuk.php (GET)
     */
    public function edit(int $id)
    {
        $masuk     = BarangMasuk::with('barang')->findOrFail($id);
        $suppliers = Supplier::orderBy('nama_supplier')->get();
        return view('barang_masuk.edit', compact('masuk', 'suppliers'));
    }

    /**
     * Update barang masuk + recalculate stok (selisih).
     * Native: edit_barang_masuk.php (POST)
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'id_supplier' => 'required|exists:supplier,id_supplier',
            'tanggal'     => 'required|date',
            'jumlah'      => 'required|integer|min:1',
        ]);

        $masuk       = BarangMasuk::findOrFail($id);
        $jumlahLama  = $masuk->jumlah_masuk;
        $jumlahBaru  = $request->jumlah;
        $selisih     = $jumlahBaru - $jumlahLama;

        $masuk->update([
            'id_supplier'  => $request->id_supplier,
            'tanggal_masuk'=> $request->tanggal,
            'jumlah_masuk' => $jumlahBaru,
        ]);

        // Adjust stok sesuai selisih
        Barang::where('id_barang', $masuk->id_barang)
              ->increment('stok', $selisih);

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Data berhasil diupdate! Stok otomatis disesuaikan.');
    }

    /**
     * Hapus barang masuk + kurangi stok.
     * Native: hapus_barang_masuk.php
     */
    public function destroy(int $id)
    {
        $masuk = BarangMasuk::findOrFail($id);

        // Kembalikan stok
        Barang::where('id_barang', $masuk->id_barang)
              ->decrement('stok', $masuk->jumlah_masuk);

        $masuk->delete();

        return redirect()->route('barang-masuk.index')
            ->with('success', 'Data barang masuk dihapus dan stok dikembalikan.');
    }
}
