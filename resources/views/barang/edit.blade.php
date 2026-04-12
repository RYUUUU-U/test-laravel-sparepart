@extends('layouts.app')
@section('title', 'Edit Barang')
@section('content')

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Edit Data Barang</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('barang.update', $barang->id_barang) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label>Kode Barang</label>
                        <input type="text" class="form-control bg-light" value="{{ $barang->kode_barang }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label>Nama Barang</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama', $barang->nama_barang) }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Harga Beli (Rp)</label>
                            <input type="text" name="beli" id="rupiah1" class="form-control"
                                   value="{{ old('beli', number_format($barang->harga_beli, 0, ',', '.')) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Harga Jual (Rp)</label>
                            <input type="text" name="jual" id="rupiah2" class="form-control"
                                   value="{{ old('jual', number_format($barang->harga_jual, 0, ',', '.')) }}" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Stok</label>
                            <input type="number" name="stok" class="form-control"
                                   value="{{ old('stok', $barang->stok) }}" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Satuan</label>
                            <input type="text" name="satuan" class="form-control"
                                   value="{{ old('satuan', $barang->satuan) }}" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Data</button>
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function formatRupiah(angka) {
        var number_string = angka.replace(/[^,\d]/g, '').toString(),
            split = number_string.split(','), sisa = split[0].length % 3,
            rupiah = split[0].substr(0, sisa),
            ribuan = split[0].substr(sisa).match(/\d{3}/gi);
        if (ribuan) { separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
        return rupiah;
    }
    document.getElementById('rupiah1').addEventListener('keyup', function(){ this.value = formatRupiah(this.value); });
    document.getElementById('rupiah2').addEventListener('keyup', function(){ this.value = formatRupiah(this.value); });
</script>
@endpush
