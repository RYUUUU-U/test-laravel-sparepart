@extends('layouts.app')
@section('title', 'Data User')
@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data User / Pengguna</h1>
    <a href="{{ route('user.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah User
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th width="5%">No</th>
                <th>Nama Lengkap</th>
                <th>Username</th>
                <th>Role (Hak Akses)</th>
                <th width="15%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($users as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $d->nama_lengkap }}</td>
                <td>{{ $d->username }}</td>
                <td>
                    @if($d->role == 'admin')
                        <span class="badge bg-success">Admin</span>
                    @elseif($d->role == 'owner')
                        <span class="badge bg-primary">Owner</span>
                    @else
                        <span class="badge bg-secondary">Petugas/Kasir</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('user.edit', $d->id_user) }}" class="btn btn-warning btn-sm">Edit</a>
                    @if($d->username !== session('username'))
                    <form action="{{ route('user.destroy', $d->id_user) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin hapus user ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                    @endif
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Belum ada data user.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
