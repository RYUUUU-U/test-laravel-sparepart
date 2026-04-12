@extends('layouts.app')
@section('title', 'Barang Masuk')
@section('content')

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fa-solid fa-dolly"></i> Laporan Barang Masuk (Restock)</h1>
    @if(in_array(session('role'), ['admin', 'kasir']))
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="{{ route('barang-masuk.create') }}" class="btn btn-success fw-bold shadow-sm">
            <i class="fa-solid fa-plus me-2"></i> Input Barang Masuk
        </a>
    </div>
    @endif
</div>

{{-- Filter --}}
<div class="card shadow mb-4">
    <div class="card-header bg-light py-2">
        <h6 class="m-0 font-weight-bold text-dark small"><i class="fa-solid fa-filter me-1"></i> Filter Laporan</h6>
    </div>
    <div class="card-body py-3">
        <form method="GET" action="{{ route('barang-masuk.index') }}">
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
                    <button type="submit" class="btn btn-primary btn-sm fw-bold"><i class="fa-solid fa-filter"></i> Terapkan</button>
                    <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary btn-sm"><i class="fa-solid fa-rotate"></i> Reset</a>
                    <div class="btn-group ms-2">
                        <button type="button" class="btn btn-success btn-sm dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="fa-solid fa-download"></i> Export
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" target="_blank" href="{{ route('laporan.export-masuk', ['tgl_mulai'=>$tglMulai,'tgl_selesai'=>$tglSelesai]) }}">Excel</a></li>
                            <li><a class="dropdown-item" target="_blank" href="{{ route('laporan.cetak-masuk', ['tgl_mulai'=>$tglMulai,'tgl_selesai'=>$tglSelesai]) }}">PDF / Print</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- Tabel Data --}}
<div class="card shadow mb-4">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-success">Data Restock</h6>
        <form method="GET" action="{{ route('barang-masuk.index') }}" class="d-flex align-items-center">
            <input type="hidden" name="tgl_mulai" value="{{ $tglMulai }}">
            <input type="hidden" name="tgl_selesai" value="{{ $tglSelesai }}">
            <span class="small me-2 text-muted">Show:</span>
            <select name="limit" class="form-select form-select-sm" style="width: auto;" onchange="this.form.submit()">
                @foreach([10,25,50,100] as $opt)
                <option value="{{ $opt }}" {{ $limit == $opt ? 'selected' : '' }}>{{ $opt }}</option>
                @endforeach
            </select>
        </form>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th width="5%">No</th>
                        <th>Waktu Masuk</th>
                        <th>Nama Barang</th>
                        <th>Supplier</th>
                        <th class="text-center">Jumlah</th>
                        @if(session('role') == 'admin')
                        <th class="text-center" width="10%">Aksi</th>
                        @endif
                    </tr>
                </thead>
                <tbody>
                    @forelse($dataMasuk as $d)
                    <tr>
                        <td>{{ $dataMasuk->firstItem() + $loop->index }}</td>
                        <td><b>{{ \Carbon\Carbon::parse($d->tanggal_masuk)->format('d/m/Y H:i') }} WIB</b></td>
                        <td>{{ $d->barang->nama_barang ?? '-' }}</td>
                        <td>{{ $d->supplier->nama_supplier ?? '-' }}</td>
                        <td class="text-center fw-bold text-success">+ {{ $d->jumlah_masuk }}</td>
                        @if(session('role') == 'admin')
                        <td class="text-center">
                            <a href="{{ route('barang-masuk.edit', $d->id_masuk) }}" class="btn btn-warning btn-sm text-white">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <form action="{{ route('barang-masuk.destroy', $d->id_masuk) }}" method="POST" class="d-inline"
                                  onsubmit="return confirm('Yakin hapus? Stok barang akan dikurangi otomatis.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm"><i class="fa-solid fa-trash"></i></button>
                            </form>
                        </td>
                        @endif
                    </tr>
                    @empty
                    <tr><td colspan="6" class="text-center py-5 text-muted">Data tidak ditemukan.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex justify-content-between align-items-center mt-3">
            <small class="text-muted">Halaman {{ $dataMasuk->currentPage() }} dari {{ $dataMasuk->lastPage() }} (Total: {{ $dataMasuk->total() }})</small>
            {{ $dataMasuk->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>
@endsection
