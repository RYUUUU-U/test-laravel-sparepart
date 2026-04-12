@extends('layouts.app')

@section('title', 'Dashboard Kasir')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Halo, {{ session('nama_lengkap') }}</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-outline-secondary">
            <i class="fa-regular fa-calendar me-1"></i> {{ now()->translatedFormat('d F Y') }}
        </button>
    </div>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-info shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0">Total Jenis Barang</h6>
                    <h2 class="my-2 fw-bold">{{ $jmlBarang }} Item</h2>
                </div>
                <i class="fa-solid fa-boxes-stacked fa-4x opacity-25"></i>
            </div>
            <div class="card-footer bg-transparent border-0 small">
                <a class="text-white text-decoration-none" href="{{ route('barang.kasir') }}">
                    Lihat Detail Gudang <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0">Barang Masuk (Hari Ini)</h6>
                    <h2 class="my-2 fw-bold">{{ $jmlTransaksiMasuk }} Transaksi</h2>
                </div>
                <i class="fa-solid fa-dolly fa-4x opacity-25"></i>
            </div>
            <div class="card-footer bg-transparent border-0 small">
                <a class="text-white text-decoration-none" href="{{ route('barang-masuk.create') }}">
                    Input Masuk <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>

    <div class="col-md-4 mb-3">
        <div class="card text-white bg-warning shadow h-100">
            <div class="card-body d-flex justify-content-between align-items-center">
                <div>
                    <h6 class="card-title mb-0">Barang Keluar (Hari Ini)</h6>
                    <h2 class="my-2 fw-bold">{{ $jmlTransaksiKeluar }} Transaksi</h2>
                </div>
                <i class="fa-solid fa-truck-ramp-box fa-4x opacity-25"></i>
            </div>
            <div class="card-footer bg-transparent border-0 small">
                <a class="text-white text-decoration-none fw-bold" href="{{ route('barang-keluar.kasir') }}">
                    MULAI TRANSAKSI KASIR <i class="fa-solid fa-arrow-right"></i>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="row mt-4">
    <div class="col-md-12">
        <div class="card shadow border-danger">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="fa-solid fa-triangle-exclamation me-2"></i> Peringatan Stok Menipis (&lt;= 5)</span>
                <span class="badge bg-white text-danger">Segera Lapor!</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-striped table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4">Nama Barang</th>
                                <th>Sisa Stok</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stokMinim as $d)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $d->nama_barang }}</td>
                                <td><span class="badge bg-danger rounded-pill">{{ $d->stok }} Unit</span></td>
                            </tr>
                            @empty
                            <tr><td colspan="2" class="text-center py-2 text-success fw-bold">Aman! Tidak ada stok kritis.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
