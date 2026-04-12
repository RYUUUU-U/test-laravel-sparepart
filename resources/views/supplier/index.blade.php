@extends('layouts.app')
@section('title', 'Data Supplier')
@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Supplier</h1>
    <a href="{{ route('supplier.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Supplier
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th width="5%">No</th>
                <th>Nama Supplier</th>
                <th>No. Telepon</th>
                <th>Alamat</th>
                <th width="15%">Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($suppliers as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ $d->nama_supplier }}</td>
                <td>{{ wordwrap($d->no_telp, 4, '-', true) }}</td>
                <td>{{ $d->alamat }}</td>
                <td>
                    <a href="{{ route('supplier.edit', $d->id_supplier) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('supplier.destroy', $d->id_supplier) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin hapus data ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="text-center">Belum ada data supplier.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
