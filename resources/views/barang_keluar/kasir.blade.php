@extends('layouts.app')
@section('title', 'Riwayat Transaksi')
@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fa-solid fa-clock-rotate-left"></i> Riwayat Transaksi</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('barang-keluar.create') }}" class="btn btn-primary fw-bold shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Input Transaksi Baru
        </a>
    </div>
</div>

{{-- Filter --}}
<div class="card shadow mb-4">
    <div class="card-header bg-light py-2">
        <h6 class="m-0 font-weight-bold text-dark small"><i class="fa-solid fa-filter me-1"></i> Filter Tanggal</h6>
    </div>
    <div class="card-body py-3">
        <form method="GET" action="{{ route('barang-keluar.kasir') }}">
            <input type="hidden" name="limit" value="{{ $limit }}">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold small mb-1">Dari Tanggal</label>
                    <input type="date" name="tgl_mulai" class="form-control form-control-sm" value="{{ $tglMulai }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold small mb-1">Sampai Tanggal</label>
                    <input type="date" name="tgl_selesai" class="form-control form-control-sm" value="{{ $tglSelesai }}">
                </div>
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="fa-solid fa-magnifying-glass"></i> Filter</button>
                    <a href="{{ route('barang-keluar.kasir') }}" class="btn btn-secondary btn-sm"><i class="fa-solid fa-rotate"></i> Reset</a>
                    <div class="btn-group ms-2">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" target="_blank" href="{{ route('laporan.export-keluar', ['tgl_mulai'=>$tglMulai,'tgl_selesai'=>$tglSelesai]) }}">Excel</a></li>
                            <li><a class="dropdown-item" target="_blank" href="{{ route('laporan.cetak-keluar', ['tgl_mulai'=>$tglMulai,'tgl_selesai'=>$tglSelesai]) }}">PDF / Print</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel --}}
<div class="card shadow mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">Data Transaksi</h6>
        <form method="GET" action="{{ route('barang-keluar.kasir') }}" class="d-flex align-items-center">
            <input type="hidden" name="tgl_mulai" value="{{ $tglMulai }}">
            <input type="hidden" name="tgl_selesai" value="{{ $tglSelesai }}">
            <span class="small me-2 text-muted">Tampilkan:</span>
            <select name="limit" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                @foreach([10,25,50,100] as $opt)
                <option value="{{ $opt }}" {{ $limit == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body">
        <table class="table table-bordered table-hover table-striped mb-0">
            <thead class="table-dark">
                <tr>
                    <th width="5%">No</th>
                    <th>Waktu Transaksi</th>
                    <th>Nama Barang</th>
                    <th class="text-center">Qty</th>
                    <th class="text-end">Total Harga</th>
                </tr>
            </thead>
            <tbody>
                @forelse($dataKeluar as $d)
                <tr>
                    <td>{{ $dataKeluar->firstItem() + $loop->index }}</td>
                    <td><b>{{ \Carbon\Carbon::parse($d->tanggal_keluar)->format('d/m/Y H:i') }} WIB</b></td>
                    <td>{{ $d->barang->nama_barang ?? '-' }}</td>
                    <td class="text-center fw-bold">{{ $d->jumlah_keluar }}</td>
                    <td class="text-end text-success fw-bold">Rp {{ number_format($d->total_harga) }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                @endforelse
            </tbody>
        </table>
        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Halaman {{ $dataKeluar->currentPage() }} dari {{ $dataKeluar->lastPage() }} (Total: {{ $dataKeluar->total() }})</small>
            {{ $dataKeluar->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
