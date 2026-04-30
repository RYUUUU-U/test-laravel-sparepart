<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    /**
     * Tampilkan halaman login.
     * Native: index.php
     */
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard
        if (session('status') === 'login') {
            return $this->redirectByRole(session('role'));
        }

        return view('auth.login');
    }

    /**
     * Proses login form.
     * Native: cek_login.php
     *
     * Upgrade: Password kini dicek dengan bcrypt (Hash::check).
     * Untuk user lama yang masih md5: saat login pertama kali berhasil (via md5),
     * password langsung di-rehash ke bcrypt secara otomatis.
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('username', $request->username)->first();

        if (!$user) {
            return back()->with('error', 'Username atau password salah!');
        }

        $authenticated = false;

        // Cek bcrypt terlebih dahulu (user baru / sudah di-rehash)
        // Wrap in try-catch: Laravel 13 throws RuntimeException for non-bcrypt hashes
        try {
            if (Hash::check($request->password, $user->password)) {
                $authenticated = true;
            }
        } catch (\RuntimeException $e) {
            // Password lama kemungkinan md5, lanjutkan ke fallback di bawah
        }

        // Fallback md5 untuk user lama, lalu rehash ke bcrypt secara otomatis
        if (!$authenticated && md5($request->password) === $user->password) {
            $authenticated = true;
            // Rehash otomatis ke bcrypt
            $user->password = Hash::make($request->password);
            $user->save();
        }

        if (!$authenticated) {
            return back()->with('error', 'Username atau password salah!');
        }

        // Set session (meniru struktur $_SESSION asli)
        session([
            'id_user'      => $user->id_user,
            'nama_lengkap' => $user->nama_lengkap,
            'username'     => $user->username,
            'role'         => $user->role,
            'status'       => 'login',
        ]);

        return $this->redirectByRole($user->role);
    }

    /**
     * Logout user.
     * Native: logout.php
     */
    public function logout(Request $request)
    {
        // Hapus hanya session admin — session customer (customer_id, dll) tidak tersentuh
        session()->forget([
            'id_user',
            'nama_lengkap',
            'username',
            'role',
            'status',
        ]);

        // Regenerate CSRF token for security
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Helper: redirect berdasarkan role user.
     */
    private function redirectByRole(string $role)
    {
        return match ($role) {
            'admin'  => redirect()->route('dashboard.admin'),
            'kasir'  => redirect()->route('dashboard.kasir'),
            'owner'  => redirect()->route('dashboard.owner'),
            default  => redirect()->route('login')->with('error', 'Role tidak dikenali.'),
        };
    }
}
