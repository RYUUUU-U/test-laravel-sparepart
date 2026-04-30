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
                <form method="POST" action="{{ route('barang.update', $barang->id_barang) }}" enctype="multipart/form-data">
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

                    {{-- Gambar Produk --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Gambar Produk</label>
                        @if($barang->image_url)
                            @php
                                $imgData = is_array($barang->image_url) ? $barang->image_url : json_decode($barang->image_url, true);
                                $thumbUrl = !empty($imgData['thumbnail']) ? asset('storage/' . $imgData['thumbnail']) : null;
                            @endphp
                            @if($thumbUrl)
                                <div class="mb-2">
                                    <img src="{{ $thumbUrl }}" alt="Gambar saat ini" class="img-thumbnail" style="max-height:150px;">
                                    <small class="d-block text-muted mt-1">Gambar saat ini. Unggah file baru untuk mengganti.</small>
                                </div>
                            @endif
                        @endif
                        <input type="file" name="image" id="imageInput" class="form-control @error('image') is-invalid @enderror"
                               accept="image/jpeg,image/png,image/webp">
                        <small class="text-muted">Format: JPG, PNG, WebP. Maks 5MB. Akan dikonversi otomatis ke WebP.</small>
                        @error('image')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        <div id="imagePreview" class="mt-2" style="display:none;">
                            <img id="previewImg" src="" alt="Preview" class="img-thumbnail" style="max-height:180px;">
                        </div>
                    </div>

                    {{-- Deskripsi --}}
                    <div class="mb-3">
                        <label class="form-label fw-bold">Deskripsi Produk</label>
                        <textarea name="deskripsi" class="form-control @error('deskripsi') is-invalid @enderror"
                                  rows="4" placeholder="Tulis deskripsi detail produk...">{{ old('deskripsi', $barang->deskripsi) }}</textarea>
                        @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
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
        if (ribuan) { let separator = sisa ? '.' : ''; rupiah += separator + ribuan.join('.'); }
        return rupiah;
    }
    document.getElementById('rupiah1').addEventListener('keyup', function(){ this.value = formatRupiah(this.value); });
    document.getElementById('rupiah2').addEventListener('keyup', function(){ this.value = formatRupiah(this.value); });

    // Image preview
    document.getElementById('imageInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        const preview = document.getElementById('imagePreview');
        const img = document.getElementById('previewImg');
        if (file) {
            img.src = URL.createObjectURL(file);
            preview.style.display = 'block';
        } else {
            preview.style.display = 'none';
        }
    });
</script>
@endpush
