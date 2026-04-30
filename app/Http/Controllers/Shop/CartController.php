<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Barang;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Struktur Session Cart
    |--------------------------------------------------------------------------
    | session('cart') = [
    |   id_barang (int) => [
    |     'id'       => int,
    |     'name'     => string,
    |     'code'     => string,
    |     'price'    => int,       // harga_jual snapshot saat ditambahkan
    |     'quantity' => int,
    |     'subtotal' => int,
    |   ],
    |   ...
    | ]
    |
    | Protected oleh middleware checkCustomer (didefinisikan di routes/web.php).
    */

    /**
     * Tampilkan isi keranjang belanja.
     */
    public function index()
    {
        $cart  = session('cart', []);
        $total = collect($cart)->sum('subtotal');

        return view('shop.cart', compact('cart', 'total'));
    }

    /**
     * Tambahkan produk ke keranjang.
     * POST /cart/add
     */
    public function add(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|integer|exists:barang,id_barang',
            'quantity'  => 'required|integer|min:1|max:99',
        ]);

        $id       = (int) $request->barang_id;
        $qty      = (int) $request->quantity;
        $barang   = Barang::findOrFail($id);

        // Cek ketersediaan stok
        if ($barang->stok < 1) {
            return back()->with('error', "Maaf, stok {$barang->nama_barang} habis.");
        }

        $cart = session('cart', []);

        if (isset($cart[$id])) {
            // Produk sudah di cart — tambahkan qty
            $newQty = $cart[$id]['quantity'] + $qty;

            if ($newQty > $barang->stok) {
                return back()->with('error', "Stok {$barang->nama_barang} tidak mencukupi. Tersisa {$barang->stok} unit.");
            }

            $cart[$id]['quantity'] = $newQty;
            $cart[$id]['subtotal'] = $barang->harga_jual * $newQty;
        } else {
            // Produk baru — masukkan ke cart
            if ($qty > $barang->stok) {
                return back()->with('error', "Stok {$barang->nama_barang} tidak mencukupi. Tersisa {$barang->stok} unit.");
            }

            $cart[$id] = [
                'id'       => $id,
                'name'     => $barang->nama_barang,
                'code'     => $barang->kode_barang,
                'price'    => (int) $barang->harga_jual,
                'quantity' => $qty,
                'subtotal' => (int) $barang->harga_jual * $qty,
            ];
        }

        session(['cart' => $cart]);

        return back()->with('success', "{$barang->nama_barang} berhasil ditambahkan ke keranjang.");
    }

    /**
     * Update jumlah item di keranjang.
     * POST /cart/update
     */
    public function update(Request $request)
    {
        $request->validate([
            'barang_id' => 'required|integer',
            'quantity'  => 'required|integer|min:1|max:99',
        ]);

        $id   = (int) $request->barang_id;
        $qty  = (int) $request->quantity;
        $cart = session('cart', []);

        if (! isset($cart[$id])) {
            return back()->with('error', 'Item tidak ditemukan di keranjang.');
        }

        // Validasi ulang stok saat update
        $barang = Barang::find($id);
        if ($barang && $qty > $barang->stok) {
            return back()->with('error', "Stok {$barang->nama_barang} tidak mencukupi. Tersisa {$barang->stok} unit.");
        }

        $cart[$id]['quantity'] = $qty;
        $cart[$id]['subtotal'] = $cart[$id]['price'] * $qty;

        session(['cart' => $cart]);

        return back()->with('success', 'Keranjang berhasil diperbarui.');
    }

    /**
     * Hapus satu item dari keranjang.
     * DELETE /cart/{id}
     */
    public function remove(int $id)
    {
        $cart = session('cart', []);

        if (isset($cart[$id])) {
            $name = $cart[$id]['name'];
            unset($cart[$id]);
            session(['cart' => $cart]);
            return back()->with('success', "{$name} dihapus dari keranjang.");
        }

        return back()->with('error', 'Item tidak ditemukan di keranjang.');
    }

    /**
     * Kosongkan seluruh keranjang.
     * DELETE /cart
     */
    public function clear()
    {
        session()->forget('cart');
        return back()->with('success', 'Keranjang berhasil dikosongkan.');
    }
}
