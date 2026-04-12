@extends('layouts.app')
@section('title', 'Tambah Supplier')
@section('content')

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Tambah Supplier</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('supplier.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label>Nama Supplier</label>
                        <input type="text" name="nama" class="form-control @error('nama') is-invalid @enderror"
                               value="{{ old('nama') }}" required>
                        @error('nama')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label>No. Telepon</label>
                        <input type="text" name="telp" id="no_telepon" class="form-control @error('telp') is-invalid @enderror"
                               placeholder="Contoh: 0812-xxxx-xxxx" maxlength="16" value="{{ old('telp') }}" required>
                        <small class="text-muted" style="font-size: 11px;">*Format otomatis (strip akan muncul sendiri)</small>
                        @error('telp')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control @error('alamat') is-invalid @enderror" rows="3" required>{{ old('alamat') }}</textarea>
                        @error('alamat')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>
                    <button type="submit" class="btn btn-success">Simpan Data</button>
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Kembali</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const inputTel = document.getElementById('no_telepon');
    inputTel.addEventListener('input', function (e) {
        let value = e.target.value.replace(/\D/g, '');
        value = value.replace(/(\d{4})(?=\d)/g, '$1-');
        e.target.value = value;
    });
</script>
@endpush
