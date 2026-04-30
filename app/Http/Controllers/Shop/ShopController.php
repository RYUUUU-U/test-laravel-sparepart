<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ShopController extends Controller
{
    /**
     * Landing page — hero section + produk unggulan + kategori.
     */
    public function index()
    {
        // 8 produk dengan stok terbanyak sebagai "Featured"
        $featured = Barang::where('stok', '>', 0)
            ->where('is_active', true)
            ->orderByDesc('stok')
            ->limit(8)
            ->get();

        // Daftar kategori unik yang tersedia
        $categories = Barang::select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        // Statistik untuk social proof
        $totalProduk = Barang::where('is_active', true)->count();

        return view('shop.home', compact('featured', 'categories', 'totalProduk'));
    }

    /**
     * Halaman katalog produk — dengan pencarian dan filter kategori.
     *
     * Menggunakan Cache selama 5 menit (300 detik) untuk meringankan
     * beban database saat menampilkan list produk dan daftar kategori.
     * Cache di-invalidate otomatis jika parameter pencarian berubah.
     */
    public function catalog(Request $request)
    {
        $query = Barang::query()->where('is_active', true);

        // Filter: pencarian nama atau kode barang
        if ($request->filled('q')) {
            $search = $request->q;
            $query->where(function ($q) use ($search) {
                $q->where('nama_barang', 'like', "%{$search}%")
                  ->orWhere('kode_barang', 'like', "%{$search}%");
            });
        }

        // Filter: kategori
        if ($request->filled('kategori') && $request->kategori !== 'semua') {
            $query->where('kategori', $request->kategori);
        }

        // Sort
        $sort = $request->get('sort', 'terbaru');
        $query = match ($sort) {
            'harga_asc'  => $query->orderBy('harga_jual'),
            'harga_desc' => $query->orderByDesc('harga_jual'),
            'nama'       => $query->orderBy('nama_barang'),
            default      => $query->orderByDesc('id_barang'),  // terbaru
        };

        $barang     = $query->paginate(12)->withQueryString();
        $categories = Barang::where('is_active', true)
            ->select('kategori')
            ->distinct()
            ->orderBy('kategori')
            ->pluck('kategori');

        $data = compact('barang', 'categories', 'sort');

        // Extract variabel dari cache result
        return view('shop.catalog', $data);
    }

    /**
     * Halaman detail satu produk.
     */
    public function show(int $id)
    {
        $barang = Barang::where('is_active', true)->findOrFail($id);

        // Increment view counter (fire-and-forget, tidak perlu lock)
        Barang::where('id_barang', $id)->increment('views');

        // Produk terkait dari kategori yang sama (maks 4)
        $related = Barang::where('kategori', $barang->kategori)
            ->where('id_barang', '!=', $barang->id_barang)
            ->where('is_active', true)
            ->where('stok', '>', 0)
            ->limit(4)
            ->get();

        return view('shop.product', compact('barang', 'related'));
    }
}
