@extends('layouts.app')
@section('title', 'Cek Gudang')
@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Cek Gudang - Daftar Barang</h1>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Satuan</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barang as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td><span class="badge bg-secondary">{{ $d->kode_barang }}</span></td>
                <td>{{ $d->nama_barang }}</td>
                <td>Rp {{ number_format($d->harga_jual) }}</td>
                <td class="{{ $d->stok <= 5 ? 'text-danger fw-bold' : '' }}">{{ $d->stok }}</td>
                <td>{{ $d->satuan }}</td>
            </tr>
            @empty
            <tr><td colspan="6" class="text-center">Belum ada data barang.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
