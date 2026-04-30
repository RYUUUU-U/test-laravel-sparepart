@extends('layouts.app')
@section('title', 'Data Barang')
@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Data Barang</h1>
    <a href="{{ route('barang.create') }}" class="btn btn-primary">
        <i class="fa-solid fa-plus"></i> Tambah Barang
    </a>
</div>

<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th>No</th>
                <th>Foto</th>
                <th>Kode</th>
                <th>Nama Barang</th>
                <th>Harga Beli</th>
                <th>Harga Jual</th>
                <th>Stok</th>
                <th>Satuan</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @forelse($barang as $no => $d)
            @php
                $imgData = is_array($d->image_url) ? $d->image_url : (is_string($d->image_url) ? json_decode($d->image_url, true) : null);
                $thumbSrc = !empty($imgData['thumbnail']) ? asset('storage/' . $imgData['thumbnail']) : null;
            @endphp
            <tr>
                <td>{{ $no + 1 }}</td>
                <td class="text-center" style="width:60px">
                    @if($thumbSrc)
                        <img src="{{ $thumbSrc }}" alt="{{ $d->nama_barang }}" class="rounded" style="width:45px;height:45px;object-fit:cover;">
                    @else
                        <span class="badge bg-light text-muted"><i class="fa-regular fa-image"></i></span>
                    @endif
                </td>
                <td><span class="badge bg-secondary">{{ $d->kode_barang }}</span></td>
                <td>{{ $d->nama_barang }}</td>
                <td>Rp {{ number_format($d->harga_beli) }}</td>
                <td>Rp {{ number_format($d->harga_jual) }}</td>
                <td class="{{ $d->stok <= 5 ? 'text-danger fw-bold' : '' }}">{{ $d->stok }}</td>
                <td>{{ $d->satuan }}</td>
                <td>
                    <a href="{{ route('barang.edit', $d->id_barang) }}" class="btn btn-warning btn-sm">Edit</a>
                    <form action="{{ route('barang.destroy', $d->id_barang) }}" method="POST" class="d-inline"
                          onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm">Hapus</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="9" class="text-center">Belum ada data barang.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
