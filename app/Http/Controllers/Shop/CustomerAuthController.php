<?php

namespace App\Http\Controllers\Shop;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class CustomerAuthController extends Controller
{
    // ── Register ──────────────────────────────────────────────────────────────

    /**
     * Tampilkan form registrasi pelanggan.
     */
    public function showRegister()
    {
        // Jika sudah login sebagai customer, langsung ke toko
        if (session()->has('customer_id')) {
            return redirect('/');
        }

        return view('shop.auth.register');
    }

    /**
     * Proses registrasi pelanggan baru.
     *
     * Validasi ketat: alamat lengkap (address, city, province, postal_code)
     * wajib diisi untuk keperluan pengiriman barang.
     */
    public function register(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100',
            'email'       => 'required|email|max:150|unique:customers,email',
            'password'    => 'required|string|min:8|confirmed',
            'phone'       => 'required|string|max:20',
            'address'     => 'required|string|max:500',
            'city'        => 'required|string|max:100',
            'province'    => 'required|string|max:100',
            'postal_code' => 'required|string|max:10',
        ], [
            // Nama
            'name.required'        => 'Nama lengkap wajib diisi.',
            'name.max'             => 'Nama lengkap maksimal 100 karakter.',

            // Email
            'email.required'       => 'Alamat email wajib diisi.',
            'email.email'          => 'Format email tidak valid.',
            'email.unique'         => 'Email ini sudah terdaftar. Silakan login.',
            'email.max'            => 'Email maksimal 150 karakter.',

            // Password
            'password.required'    => 'Password wajib diisi.',
            'password.min'         => 'Password minimal 8 karakter.',
            'password.confirmed'   => 'Konfirmasi password tidak cocok.',

            // Telepon
            'phone.required'       => 'Nomor telepon wajib diisi.',
            'phone.max'            => 'Nomor telepon maksimal 20 karakter.',

            // Alamat
            'address.required'     => 'Alamat lengkap wajib diisi.',
            'address.max'          => 'Alamat maksimal 500 karakter.',

            // Kota
            'city.required'        => 'Kota/kabupaten wajib diisi.',
            'city.max'             => 'Nama kota maksimal 100 karakter.',

            // Provinsi
            'province.required'    => 'Provinsi wajib diisi.',
            'province.max'         => 'Nama provinsi maksimal 100 karakter.',

            // Kode Pos
            'postal_code.required' => 'Kode pos wajib diisi.',
            'postal_code.max'      => 'Kode pos maksimal 10 karakter.',
        ]);

        $customer = Customer::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => $request->password,
            'phone'       => $request->phone,
            'address'     => $request->address,
            'city'        => $request->city,
            'province'    => $request->province,
            'postal_code' => $request->postal_code,
        ]);

        // Set session pelanggan (terpisah dari session admin)
        session([
            'customer_id'    => $customer->id,
            'customer_name'  => $customer->name,
            'customer_email' => $customer->email,
        ]);

        return redirect('/')
            ->with('success', 'Selamat datang, ' . $customer->name . '! Akun Anda berhasil dibuat.');
    }

    // ── Login ─────────────────────────────────────────────────────────────────

    /**
     * Tampilkan form login pelanggan.
     */
    public function showLogin()
    {
        // Jika sudah login sebagai customer, langsung ke toko
        if (session()->has('customer_id')) {
            return redirect('/');
        }

        return view('shop.auth.login');
    }

    /**
     * Proses login pelanggan.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
        ]);

        $customer = Customer::where('email', $request->email)->first();

        // Validasi: customer tidak ditemukan
        if (! $customer) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau password salah.');
        }

        // Validasi: password tidak cocok
        if (! Hash::check($request->password, $customer->password)) {
            return back()
                ->withInput($request->only('email'))
                ->with('error', 'Email atau password salah.');
        }

        // Set session pelanggan (terpisah dari session admin)
        session([
            'customer_id'    => $customer->id,
            'customer_name'  => $customer->name,
            'customer_email' => $customer->email,
        ]);

        // Kembalikan ke halaman yang sebelumnya dituju, atau ke toko
        $intended = session()->pull('url.intended', '/');

        return redirect($intended)
            ->with('success', 'Selamat datang kembali, ' . $customer->name . '!');
    }

    // ── Logout ────────────────────────────────────────────────────────────────

    /**
     * Logout pelanggan: hapus hanya session customer, bukan session admin.
     */
    public function logout(Request $request)
    {
        // Hapus hanya key milik customer — session admin (id_user, role, status) tidak tersentuh
        session()->forget([
            'customer_id',
            'customer_name',
            'customer_email',
        ]);

        // Regenerate token CSRF untuk keamanan
        $request->session()->regenerateToken();

        return redirect('/')
            ->with('success', 'Anda berhasil logout.');
    }
}
