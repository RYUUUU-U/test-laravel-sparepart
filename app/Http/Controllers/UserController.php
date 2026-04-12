<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /** Native: user.php */
    public function index()
    {
        $users = User::orderByDesc('id_user')->get();
        return view('user.index', compact('users'));
    }

    /** Native: tambah_user.php (GET) */
    public function create()
    {
        return view('user.create');
    }

    /** Native: tambah_user.php (POST) - now uses bcrypt */
    public function store(Request $request)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => 'required|string|max:50|unique:users,username',
            'password' => 'required|string|min:3',
            'role'     => 'required|in:admin,kasir,owner',
        ]);

        User::create([
            'nama_lengkap' => $request->nama,
            'username'     => $request->username,
            'password'     => Hash::make($request->password), // bcrypt
            'role'         => $request->role,
        ]);

        return redirect()->route('user.index')
            ->with('success', 'User berhasil ditambahkan!');
    }

    /** Native: edit_user.php (GET) */
    public function edit(int $id)
    {
        $user = User::findOrFail($id);
        return view('user.edit', compact('user'));
    }

    /** Native: edit_user.php (POST) */
    public function update(Request $request, int $id)
    {
        $request->validate([
            'nama'     => 'required|string|max:100',
            'username' => "required|string|max:50|unique:users,username,$id,id_user",
            'password' => 'nullable|string|min:3',
            'role'     => 'required|in:admin,kasir,owner',
        ]);

        $user = User::findOrFail($id);

        $data = [
            'nama_lengkap' => $request->nama,
            'username'     => $request->username,
            'role'         => $request->role,
        ];

        // Update password hanya jika diisi
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('user.index')
            ->with('success', 'Data user berhasil diupdate!');
    }

    /** Native: hapus_user.php */
    public function destroy(int $id)
    {
        $user = User::findOrFail($id);

        // Jangan hapus diri sendiri
        if ($user->username === session('username')) {
            return redirect()->route('user.index')
                ->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $user->delete();
        return redirect()->route('user.index')
            ->with('success', 'User berhasil dihapus!');
    }
}
