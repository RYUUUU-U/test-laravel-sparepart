<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerSettingsController extends Controller
{
    /**
     * Tampilkan halaman pengaturan akun.
     */
    public function edit()
    {
        $customerId = session('customer_id');

        if (! $customerId) {
            return redirect()->route('customer.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $customer = Customer::findOrFail($customerId);

        return view('shop.customer.settings', compact('customer'));
    }

    /**
     * Simpan perubahan data akun.
     */
    public function update(Request $request)
    {
        $customerId = session('customer_id');

        if (! $customerId) {
            return redirect()->route('customer.login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $customer = Customer::findOrFail($customerId);

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'phone'       => 'required|string|max:20',
            'address'     => 'required|string|max:500',
            'city'        => 'required|string|max:100',
            'province'    => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
            'password'    => 'nullable|string|min:8|confirmed',
        ]);

        // Jika password diisi, update password
        if (!empty($validated['password'])) {
            $customer->password = Hash::make($validated['password']);
        }

        $customer->name        = $validated['name'];
        $customer->phone       = $validated['phone'];
        $customer->address     = $validated['address'];
        $customer->city        = $validated['city'];
        $customer->province    = $validated['province'];
        $customer->postal_code = $validated['postal_code'];
        $customer->save();

        // Update nama di session jika berubah
        session(['customer_name' => $customer->name]);

        return back()->with('success', 'Profil dan alamat berhasil diperbarui.');
    }
}
