<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;

class SupplierController extends Controller
{
    /** Native: supplier.php */
    public function index()
    {
        $suppliers = Supplier::orderByDesc('id_supplier')->get();
        return view('supplier.index', compact('suppliers'));
    }

    /** Native: tambah_supplier.php (GET) */
    public function create()
    {
        return view('supplier.create');
    }

    /** Native: tambah_supplier.php (POST) */
    public function store(Request $request)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'telp'   => 'required|string|max:20',
            'alamat' => 'required|string',
        ]);

        Supplier::create([
            'nama_supplier' => $request->nama,
            'no_telp'       => str_replace('-', '', $request->telp),
            'alamat'        => $request->alamat,
        ]);

        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil ditambahkan!');
    }

    /** Native: edit_supplier.php (GET) */
    public function edit(int $id)
    {
        $supplier = Supplier::findOrFail($id);
        return view('supplier.edit', compact('supplier'));
    }

    /** Native: edit_supplier.php (POST) */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama'   => 'required|string|max:100',
            'telp'   => 'required|string|max:20',
            'alamat' => 'required|string',
        ]);

        Supplier::findOrFail($id)->update([
            'nama_supplier' => $request->nama,
            'no_telp'       => str_replace('-', '', $request->telp),
            'alamat'        => $request->alamat,
        ]);

        return redirect()->route('supplier.index')
            ->with('success', 'Data supplier berhasil diupdate!');
    }

    /** Native: hapus_supplier.php */
    public function destroy(int $id)
    {
        Supplier::findOrFail($id)->delete();
        return redirect()->route('supplier.index')
            ->with('success', 'Supplier berhasil dihapus!');
    }
}
