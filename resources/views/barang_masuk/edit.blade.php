@extends('layouts.app')
@section('title', 'Edit Barang Masuk')
@section('content')

<div class="row mt-4">
    <div class="col-md-6 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-warning text-white">
                <h5 class="mb-0 fw-bold"><i class="fa-solid fa-pen-to-square me-2"></i> Edit Data Barang Masuk</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('barang-masuk.update', $masuk->id_masuk) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label>Nama Barang (Tidak Bisa Diubah)</label>
                        <input type="text" class="form-control" value="{{ $masuk->barang->nama_barang ?? '-' }}"
                               readonly style="background-color: #e9ecef;">
                        <small class="text-danger">* Jika salah barang, silakan Hapus data ini dan input baru.</small>
                    </div>
                    <div class="mb-3">
                        <label>Tanggal Masuk</label>
                        <input type="date" name="tanggal" class="form-control"
                               value="{{ old('tanggal', $masuk->tanggal_masuk) }}" required>
                    </div>
                    <div class="mb-3">
                        <label>Supplier</label>
                        <select name="id_supplier" class="form-control" required>
                            @foreach($suppliers as $s)
                            <option value="{{ $s->id_supplier }}"
                                {{ (old('id_supplier', $masuk->id_supplier) == $s->id_supplier) ? 'selected' : '' }}>
                                {{ $s->nama_supplier }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label>Jumlah Masuk (Edit Angka)</label>
                        <input type="number" name="jumlah" class="form-control fw-bold border-warning"
                               value="{{ old('jumlah', $masuk->jumlah_masuk) }}" min="1" required>
                        <small class="text-muted">* Stok gudang akan otomatis bertambah/berkurang sesuai perubahan angka ini.</small>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('barang-masuk.index') }}" class="btn btn-secondary w-50">Batal</a>
                        <button type="submit" class="btn btn-warning w-50 fw-bold text-white">Update Data</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
