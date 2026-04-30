<?php

namespace App\Http\Controllers;

use App\Models\Barang;
use App\Services\BarangService;
use App\Services\ImageService;
use Illuminate\Http\Request;

class BarangController extends Controller
{
    public function __construct(
        private BarangService $barangService,
        private ImageService $imageService,
    ) {}

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
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        // Bersihkan format rupiah (titik sebagai pemisah ribuan)
        $beli = (int) str_replace('.', '', $request->beli);
        $jual = (int) str_replace('.', '', $request->jual);

        $kode = $this->barangService->generateKode($request->nama);

        // Upload gambar produk jika ada
        $imageUrl = null;
        if ($request->hasFile('image')) {
            $imageUrl = $this->imageService->uploadProductImage($request->file('image'));
        }

        Barang::create([
            'kode_barang' => $kode,
            'nama_barang' => $request->nama,
            'kategori'    => 'Umum',
            'harga_beli'  => $beli,
            'harga_jual'  => $jual,
            'stok'        => $request->stok,
            'satuan'      => $request->satuan,
            'image_url'   => $imageUrl,
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
            'image'  => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ]);

        $barang = Barang::findOrFail($id);

        $data = [
            'nama_barang' => $request->nama,
            'harga_beli'  => (int) str_replace('.', '', $request->beli),
            'harga_jual'  => (int) str_replace('.', '', $request->jual),
            'stok'        => $request->stok,
            'satuan'      => $request->satuan,
        ];

        // Upload gambar baru jika ada, hapus yang lama
        if ($request->hasFile('image')) {
            $this->imageService->deleteProductImages($barang->image_url);
            $data['image_url'] = $this->imageService->uploadProductImage($request->file('image'));
        }

        $barang->update($data);

        return redirect()->route('barang.index')
            ->with('success', 'Data barang berhasil diupdate!');
    }

    /**
     * Hapus barang. Native: hapus_barang.php
     */
    public function destroy(int $id)
    {
        $barang = Barang::findOrFail($id);

        // Hapus gambar dari storage sebelum hapus data
        $this->imageService->deleteProductImages($barang->image_url);

        $barang->delete();
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
