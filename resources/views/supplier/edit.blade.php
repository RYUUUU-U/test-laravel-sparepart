@extends('layouts.app')
@section('title', 'Edit Supplier')
@section('content')

<div class="row">
    <div class="col-md-6 mx-auto">
        <div class="card shadow">
            <div class="card-header bg-warning">
                <h5 class="mb-0">Edit Supplier</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('supplier.update', $supplier->id_supplier) }}">
                    @csrf @method('PUT')
                    <div class="mb-3">
                        <label>Nama Supplier</label>
                        <input type="text" name="nama" class="form-control"
                               value="{{ old('nama', $supplier->nama_supplier) }}" required>
                    </div>
                    <div class="mb-3">
                        <label>No. Telepon</label>
                        <input type="text" name="telp" id="no_telepon" class="form-control"
                               value="{{ old('telp', $supplier->no_telp) }}" maxlength="16" required>
                    </div>
                    <div class="mb-3">
                        <label>Alamat</label>
                        <textarea name="alamat" class="form-control" rows="3" required>{{ old('alamat', $supplier->alamat) }}</textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Data</button>
                    <a href="{{ route('supplier.index') }}" class="btn btn-secondary">Batal</a>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const inputTelEdit = document.getElementById('no_telepon');
    function formatTelepon(input) {
        let value = input.value.replace(/\D/g, '');
        value = value.replace(/(\d{4})(?=\d)/g, '$1-');
        input.value = value;
    }
    inputTelEdit.addEventListener('input', function (e) { formatTelepon(e.target); });
    formatTelepon(inputTelEdit);
</script>
@endpush
