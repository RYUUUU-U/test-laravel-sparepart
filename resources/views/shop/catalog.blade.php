@extends('layouts.shop')

@section('title', 'Katalog Produk — MotorParts Store')
@section('meta_description', 'Jelajahi ribuan sparepart motor. Filter berdasarkan kategori dan cari produk yang Anda butuhkan.')

@push('styles')
<style>
    .catalog-header {
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        padding: 2.5rem 0;
        color: #fff;
    }
    .catalog-header h1 { font-size: 1.9rem; font-weight: 800; margin-bottom: .25rem; }
    .catalog-header p { color: rgba(255,255,255,.6); margin: 0; font-size: .9rem; }

    .search-bar-wrap { background: #fff; border-radius: 12px; padding: 1.5rem; box-shadow: 0 2px 16px rgba(0,0,0,.08); margin-top: -1rem; position: relative; z-index: 10; }
    .search-bar-wrap .form-control, .search-bar-wrap .form-select {
        border-radius: 8px;
        border: 1.5px solid #e5e7eb;
        padding: .6rem 1rem;
        font-size: .9rem;
    }
    .search-bar-wrap .form-control:focus, .search-bar-wrap .form-select:focus {
        border-color: #E63946;
        box-shadow: 0 0 0 3px rgba(230,57,70,.1);
    }

    .result-count { font-size: .9rem; color: #6b7280; }
    .result-count strong { color: #1a1a2e; }

    .product-card .card-body { padding: 1rem; }
    .product-card .nama { font-weight: 700; font-size: .95rem; color: #1a1a2e; text-decoration: none; }
    .product-card .nama:hover { color: #E63946; }
    .add-cart-form { position: relative; z-index: 1; }

    .pagination .page-link {
        border-radius: 8px !important;
        margin: 0 2px;
        border: 1.5px solid #e5e7eb;
        color: #1a1a2e;
        font-weight: 500;
    }
    .pagination .page-link:hover { background: #E63946; border-color: #E63946; color: #fff; }
    .pagination .page-item.active .page-link { background: #E63946; border-color: #E63946; }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="catalog-header">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-2">
                <li class="breadcrumb-item"><a href="{{ route('shop.home') }}" class="text-white-50">Home</a></li>
                <li class="breadcrumb-item active text-white">Katalog</li>
            </ol>
        </nav>
        <h1><i class="fa-solid fa-th-large me-2"></i>Katalog Produk</h1>
        <p>Temukan sparepart motor yang Anda butuhkan</p>
    </div>
</div>

@php
    $searchQuery = is_string(request('q')) ? request('q') : '';
    $selectedKategori = is_string(request('kategori')) ? request('kategori') : '';
@endphp

<div class="container py-4">

    {{-- ── Search & Filter Bar ─────────────────────────────────── --}}
    <div class="search-bar-wrap mb-4">
        <form method="GET" action="{{ route('shop.catalog') }}" id="filterForm">
            <div class="row g-3 align-items-end">
                {{-- Search input --}}
                <div class="col-md-5">
                    <label class="form-label small fw-600 mb-1">Cari Produk</label>
                    <div class="input-group">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="fa-solid fa-search text-muted"></i>
                        </span>
                        <input type="text" name="q" class="form-control border-start-0 ps-0"
                               placeholder="Nama atau kode barang..."
                               value="{{ $searchQuery }}">
                    </div>
                </div>
                {{-- Category filter --}}
                <div class="col-md-3">
                    <label class="form-label small fw-600 mb-1">Kategori</label>
                    <select name="kategori" class="form-select" onchange="this.form.submit()">
                        <option value="semua">Semua Kategori</option>
                        @foreach($categories as $item)
                            @php
                                $catName = is_string($item) ? $item : (is_array($item) ? ($item['kategori'] ?? '') : ($item->kategori ?? ''));
                            @endphp
                            @if($catName)
                                <option value="{{ $catName }}" {{ $selectedKategori == $catName ? 'selected' : '' }}>
                                    {{ $catName }}
                                </option>
                            @endif
                        @endforeach
                    </select>
                </div>
                {{-- Sort --}}
                <div class="col-md-2">
                    <label class="form-label small fw-600 mb-1">Urutkan</label>
                    <select name="sort" class="form-select" onchange="this.form.submit()">
                        <option value="terbaru"   {{ $sort == 'terbaru'   ? 'selected' : '' }}>Terbaru</option>
                        <option value="harga_asc" {{ $sort == 'harga_asc' ? 'selected' : '' }}>Harga ↑</option>
                        <option value="harga_desc"{{ $sort == 'harga_desc'? 'selected' : '' }}>Harga ↓</option>
                        <option value="nama"      {{ $sort == 'nama'      ? 'selected' : '' }}>A–Z</option>
                    </select>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="submit" class="btn btn-primary-shop flex-grow-1">
                        <i class="fa-solid fa-search"></i> Cari
                    </button>
                    @if(request()->hasAny(['q', 'kategori', 'sort']))
                        <a href="{{ route('shop.catalog') }}" class="btn btn-outline-secondary">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    @endif
                </div>
            </div>
        </form>
    </div>

    {{-- Result count + active filters --}}
    <div class="d-flex align-items-center justify-content-between mb-3 flex-wrap gap-2">
        <p class="result-count mb-0">
            Menampilkan <strong>{{ $barang->firstItem() }}–{{ $barang->lastItem() }}</strong>
            dari <strong>{{ $barang->total() }}</strong> produk
            @if($searchQuery)
                untuk "<strong>{{ $searchQuery }}</strong>"
            @endif
        </p>
        @if($selectedKategori && $selectedKategori !== 'semua')
            <span class="badge bg-dark px-3 py-2">
                <i class="fa-solid fa-tag me-1"></i>{{ $selectedKategori }}
                <a href="{{ route('shop.catalog', array_diff_key(request()->all(), ['kategori' => ''])) }}"
                   class="text-white ms-2 text-decoration-none">&times;</a>
            </span>
        @endif
    </div>

    {{-- ── Product Grid ─────────────────────────────────────────── --}}
    <div class="row g-4">
        @forelse($barang as $b)
        @php
            $imgData = is_array($b->image_url) ? $b->image_url : (is_string($b->image_url) ? json_decode($b->image_url, true) : null);
            $thumbSrc = !empty($imgData['thumbnail']) ? asset('storage/' . $imgData['thumbnail']) : null;
            $fallbackSrc = 'https://placehold.co/400x300/e2e8f0/64748b?text=' . urlencode($b->kode_barang);
        @endphp
        <div class="col-6 col-md-4 col-lg-3">
            <div class="product-card card h-100">
                <a href="{{ route('shop.product', $b->id_barang) }}">
                    <img src="{{ $thumbSrc ?? $fallbackSrc }}"
                         alt="{{ $b->nama_barang }}" loading="lazy"
                         style="height:180px;object-fit:cover;width:100%"
                         onerror="this.src='{{ $fallbackSrc }}'">
                </a>
                <div class="card-body d-flex flex-column">
                    <span class="text-muted" style="font-size:.75rem">{{ $b->kode_barang }}</span>
                    <a href="{{ route('shop.product', $b->id_barang) }}" class="nama my-1 flex-grow-1 d-block">
                        {{ $b->nama_barang }}
                    </a>

                    {{-- Stock badge --}}
                    @if($b->stok > 5)
                        <span class="badge badge-stok-ok mb-2">Stok: {{ $b->stok }} {{ $b->satuan }}</span>
                    @elseif($b->stok > 0)
                        <span class="badge badge-stok-low mb-2">Sisa: {{ $b->stok }} {{ $b->satuan }}</span>
                    @else
                        <span class="badge badge-stok-out mb-2">Stok Habis</span>
                    @endif

                    <div class="price mb-2">Rp {{ number_format($b->harga_jual, 0, ',', '.') }}</div>

                    @if($b->stok > 0)
                        {{-- Quick add to cart form --}}
                        <form action="{{ route('shop.cart.add') }}" method="POST" class="add-cart-form">
                            @csrf
                            <input type="hidden" name="barang_id" value="{{ $b->id_barang }}">
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary-shop btn-sm w-100">
                                <i class="fa-solid fa-cart-plus me-1"></i>Tambah
                            </button>
                        </form>
                    @else
                        <button class="btn btn-secondary btn-sm w-100" disabled>Stok Habis</button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <i class="fa-solid fa-magnifying-glass fa-3x text-muted mb-3 d-block"></i>
            <h5 class="text-muted">Produk tidak ditemukan</h5>
            <p class="text-muted small">Coba kata kunci atau kategori yang berbeda.</p>
            <a href="{{ route('shop.catalog') }}" class="btn btn-primary-shop px-4">Reset Filter</a>
        </div>
        @endforelse
    </div>

    {{-- ── Pagination ───────────────────────────────────────────── --}}
    @if($barang->hasPages())
    <div class="d-flex justify-content-center mt-5">
        {{ $barang->links('pagination::bootstrap-5') }}
    </div>
    @endif
</div>
@endsection
