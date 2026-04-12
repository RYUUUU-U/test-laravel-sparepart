@extends('layouts.app')
@section('title', 'Tambah Barang')
@section('content')

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tambah Barang (Kode Otomatis)</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info py-2">
                    <small><i class="fa fa-info-circle"></i> Kode Barang akan dibuat otomatis dari Nama Barang.</small>
                </div>

                <form method="POST" action="{{ route('barang.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label>Nama Barang</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                               placeholder="Contoh: Oli Mesin, Kampas Rem" value="{{ old('nama') }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Harga Beli (Rp)</label>
                            <input type="text" name="beli" id="rupiah1" class="form-control @error('beli') is-invalid @enderror"
                                   value="{{ old('beli') }}" required>
                            @error('beli')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Harga Jual (Rp)</label>
                            <input type="text" name="jual" id="rupiah2" class="form-control @error('jual') is-invalid @enderror"
                                   value="{{ old('jual') }}" required>
                            @error('jual')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label>Stok Awal</label>
                            <input type="number" name="stok" class="form-control @error('stok') is-invalid @enderror"
                                   value="{{ old('stok', 0) }}" required>
                            @error('stok')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label>Satuan</label>
                            <input type="text" name="satuan" class="form-control @error('satuan') is-invalid @enderror"
                                   placeholder="Pcs/Box/Unit" value="{{ old('satuan') }}" required>
                            @error('satuan')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Data</button>
                    <a href="{{ route('barang.index') }}" class="btn btn-secondary">Kembali</a>
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
