<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Services\BarangService;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function __construct(private BarangService $barangService) {}

    /**
     * Daftar semua barang. Native: barang.php
     */
    public function index()
    {
        $barang = Barang::orderByDesc('id_barang')->get();
        return view('barang.index', compact('barang'));
    }

    /**
     * Halaman tambah barang. Native: tambah_barang.php (GET)
     */
    public function create()
    {
        return view('barang.create');
    }

    /**
     * Simpan barang baru. Native: tambah_barang.php (POST)
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'beli'   => 'required',
            'jual'   => 'required',
            'stok'   => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
        ]);

        // Bersihkan format rupiah (titik sebagai pemisah ribuan)
        $beli = (int) str_replace('.', '', $request->beli);
        $jual = (int) str_replace('.', '', $request->jual);

        $kode = $this->barangService->generateKode($request->nama);

        Barang::create([
            'kode_barang' => $kode,
            'nama_barang' => $request->nama,
            'kategori'    => 'Umum',
            'harga_beli'  => $beli,
            'harga_jual'  => $jual,
            'stok'        => $request->stok,
            'satuan'      => $request->satuan,
        ]);

        return redirect()->route('barang.index')
            ->with('success', "Barang berhasil ditambahkan! Kode: $kode");
    }

    /**
     * Halaman edit barang. Native: edit_barang.php (GET)
     */
    public function edit(int $id)
    {
        $barang = Barang::findOrFail($id);
        return view('barang.edit', compact('barang'));
    }

    /**
     * Update barang. Native: edit_barang.php (POST)
     */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'beli'   => 'required',
            'jual'   => 'required',
            'stok'   => 'required|integer|min:0',
            'satuan' => 'required|string|max:20',
        ]);

        $barang = Barang::findOrFail($id);

        $barang->update([
            'nama_barang' => $request->nama,
            'harga_beli'  => (int) str_replace('.', '', $request->beli),
            'harga_jual'  => (int) str_replace('.', '', $request->jual),
            'stok'        => $request->stok,
            'satuan'      => $request->satuan,
        ]);

        return redirect()->route('barang.index')
            ->with('success', 'Data barang berhasil diupdate!');
    }

    /**
     * Hapus barang. Native: hapus_barang.php
     */
    public function destroy(int $id)
    {
        Barang::findOrFail($id)->delete();
        return redirect()->route('barang.index')
            ->with('success', 'Data barang berhasil dihapus!');
    }

    /**
     * View gudang untuk kasir. Native: barang_kasir.php
     */
    public function kasir()
    {
        $barang = Barang::orderBy('nama_barang')->get();
        return view('barang.kasir', compact('barang'));
    }
}
