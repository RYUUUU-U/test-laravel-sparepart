@extends('layouts.app')
@section('title', 'Input Barang Keluar')
@section('content')

<div class="row mt-4">
    <div class="col-md-6 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-cart-shopping me-2"></i> Input Barang Keluar</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('barang-keluar.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="fw-bold">Tanggal Transaksi</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ now()->format('Y-m-d') }}" readonly>
                        <small class="text-muted">* Jam otomatis tersimpan saat tombol Simpan ditekan.</small>
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Pilih Barang</label>
                        <select name="id_barang" class="form-control @error('id_barang') is-invalid @enderror" required size="5">
                            <option value="" disabled selected>-- Pilih Barang --</option>
                            @foreach($barang as $b)
                            <option value="{{ $b->id_barang }}" {{ old('id_barang') == $b->id_barang ? 'selected' : '' }}>
                                {{ $b->nama_barang }} (Sisa: {{ $b->stok }}) - Rp {{ number_format($b->harga_jual) }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label class="fw-bold">Jumlah Keluar (Qty)</label>
                        <input type="number" name="jumlah" class="form-control form-control-lg fw-bold"
                               min="1" placeholder="0" value="{{ old('jumlah') }}" required>
                        @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('barang-keluar.kasir') }}" class="btn btn-secondary w-50">
                            <i class="fa-solid fa-arrow-left me-2"></i> Kembali
                        </a>
                        <button type="submit" class="btn btn-danger w-50 fw-bold">
                            <i class="fa-solid fa-save me-2"></i> Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
