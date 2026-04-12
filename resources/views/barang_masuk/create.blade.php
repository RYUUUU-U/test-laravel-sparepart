@extends('layouts.app')
@section('title', 'Input Barang Masuk')
@section('content')

<div class="row mt-4">
    <div class="col-md-6 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-dolly me-2"></i> Input Barang Masuk (Restock)</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('barang-masuk.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal" class="form-control" value="{{ now()->format('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Pilih Barang</label>
                        <select name="id_barang" class="form-control @error('id_barang') is-invalid @enderror" required>
                            <option value="">-- Pilih Barang --</option>
                            @foreach($barang as $b)
                            <option value="{{ $b->id_barang }}" {{ old('id_barang') == $b->id_barang ? 'selected' : '' }}>
                                {{ $b->nama_barang }} (Stok: {{ $b->stok }})
                            </option>
                            @endforeach
                        </select>
                        @error('id_barang')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label>Supplier</label>
                        <select name="id_supplier" class="form-control @error('id_supplier') is-invalid @enderror" required>
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id_supplier }}" {{ old('id_supplier') == $s->id_supplier ? 'selected' : '' }}>
                                {{ $s->nama_supplier }}
                            </option>
                            @endforeach
                        </select>
                        @error('id_supplier')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Masuk</label>
                        <input type="number" name="jumlah" class="form-control @error('jumlah') is-invalid @enderror"
                               min="1" value="{{ old('jumlah') }}" required>
                        @error('jumlah')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="d-flex gap-2">
                        @php
                            $kembali = session('role') === 'kasir' ? route('dashboard.kasir') : route('barang-masuk.index');
                        @endphp
                        <a href="{{ $kembali }}" class="btn btn-secondary w-50">Kembali</a>
                        <button type="submit" class="btn btn-success w-50 fw-bold">Simpan Stok</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
