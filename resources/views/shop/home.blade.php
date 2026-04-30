@extends('layouts.shop')

@section('title', 'Beranda — Toko Sparepart Motor')
@section('meta_description', 'Beli sparepart motor berkualitas online. Harga terbaik, pengiriman cepat.')

@push('styles')
<style>
    /* ── HERO ────────────────────────────────────────────────── */
    .hero {
        background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
        min-height: 520px;
        display: flex;
        align-items: center;
        position: relative;
        overflow: hidden;
    }
    .hero::before {
        content: '';
        position: absolute; inset: 0;
        background: radial-gradient(ellipse at 70% 50%, rgba(230,57,70,.18) 0%, transparent 60%);
    }
    .hero-badge {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: rgba(230,57,70,.18);
        border: 1px solid rgba(230,57,70,.4);
        color: #f4a261;
        border-radius: 20px;
        padding: .3rem .9rem;
        font-size: .82rem;
        font-weight: 600;
        margin-bottom: 1.2rem;
        backdrop-filter: blur(4px);
    }
    .hero h1 {
        font-size: clamp(2rem, 5vw, 3.2rem);
        font-weight: 800;
        color: #fff;
        line-height: 1.15;
        margin-bottom: 1rem;
    }
    .hero h1 span { color: #E63946; }
    .hero p {
        color: rgba(255,255,255,.7);
        font-size: 1.08rem;
        max-width: 480px;
        margin-bottom: 2rem;
    }
    .hero-stats {
        display: flex;
        gap: 2rem;
        margin-top: 2.5rem;
        flex-wrap: wrap;
    }
    .hero-stat { text-align: center; }
    .hero-stat strong { display: block; font-size: 1.6rem; font-weight: 800; color: #fff; }
    .hero-stat span { font-size: .8rem; color: rgba(255,255,255,.55); }
    .hero-visual {
        font-size: 12rem;
        opacity: .08;
        position: absolute;
        right: -2rem;
        top: 50%;
        transform: translateY(-50%) rotate(-15deg);
        color: #fff;
        pointer-events: none;
    }

    /* ── CATEGORIES STRIP ───────────────────────────────────── */
    .categories-strip { padding: 2.5rem 0; background: #fff; border-bottom: 1px solid #f0f0f0; }
    .cat-pill {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: var(--light-bg, #f8f9fa);
        border: 1.5px solid #e9ecef;
        border-radius: 40px;
        padding: .5rem 1.1rem;
        font-size: .88rem;
        font-weight: 600;
        color: #1a1a2e;
        text-decoration: none;
        transition: all .2s;
    }
    .cat-pill:hover, .cat-pill.active {
        background: #1a1a2e;
        color: #fff;
        border-color: #1a1a2e;
    }

    /* ── SECTION TITLES ─────────────────────────────────────── */
    .section-title {
        font-size: 1.7rem;
        font-weight: 800;
        color: #1a1a2e;
        margin-bottom: .4rem;
    }
    .section-sub { color: #6b7280; font-size: .95rem; margin-bottom: 2rem; }

    /* ── WHY US BANNER ──────────────────────────────────────── */
    .why-us {
        background: linear-gradient(135deg, #1a1a2e, #0f3460);
        border-radius: 20px;
        padding: 3rem;
        margin: 3rem 0;
        color: #fff;
    }
    .why-item { text-align: center; }
    .why-icon {
        width: 64px; height: 64px;
        border-radius: 50%;
        background: rgba(230,57,70,.2);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.6rem;
        color: #E63946;
        margin: 0 auto 1rem;
    }
    .why-item h6 { font-weight: 700; margin-bottom: .3rem; }
    .why-item p { font-size: .85rem; color: rgba(255,255,255,.6); margin: 0; }
</style>
@endpush

@section('content')

{{-- ══ HERO ═══════════════════════════════════════════════════════════════ --}}
<section class="hero">
    <i class="fa-solid fa-motorcycle hero-visual"></i>
    <div class="container position-relative">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <div class="hero-badge">
                    <i class="fa-solid fa-bolt"></i> Sparepart Original & Berkualitas
                </div>
                <h1>Temukan <span>Sparepart</span><br>Motor Terbaik</h1>
                <p>Ribuan pilihan sparepart motor untuk semua merek. Harga transparan, stok real-time, langsung dari gudang kami.</p>

                <div class="d-flex gap-3 flex-wrap">
                    <a href="{{ route('shop.catalog') }}" class="btn btn-danger btn-lg px-4 fw-600 rounded-3">
                        <i class="fa-solid fa-search me-2"></i>Lihat Katalog
                    </a>
                    <a href="#featured" class="btn btn-outline-light btn-lg px-4 rounded-3">
                        Produk Unggulan
                    </a>
                </div>

                <div class="hero-stats">
                    <div class="hero-stat">
                        <strong>{{ number_format($totalProduk) }}+</strong>
                        <span>Produk Tersedia</span>
                    </div>
                    <div class="hero-stat">
                        <strong>100%</strong>
                        <span>Stok Real-Time</span>
                    </div>
                    <div class="hero-stat">
                        <strong>Gratis</strong>
                        <span>Ongkos Kirim</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- ══ CATEGORIES STRIP ════════════════════════════════════════════════════ --}}
@if($categories->count())
<section class="categories-strip">
    <div class="container">
        <div class="d-flex gap-2 flex-wrap align-items-center">
            <span class="text-muted small fw-600 me-2">Kategori:</span>
            <a href="{{ route('shop.catalog') }}" class="cat-pill active">
                <i class="fa-solid fa-border-all"></i> Semua
            </a>
            @foreach($categories as $cat)
                <a href="{{ route('shop.catalog', ['kategori' => $cat]) }}" class="cat-pill">
                    <i class="fa-solid fa-tag"></i> {{ $cat }}
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- ══ FEATURED PRODUCTS ═══════════════════════════════════════════════════ --}}
<section class="py-5" id="featured">
    <div class="container">
        <div class="d-flex align-items-end justify-content-between mb-4 flex-wrap gap-2">
            <div>
                <h2 class="section-title mb-1">Produk Unggulan</h2>
                <p class="section-sub mb-0">Sparepart paling banyak tersedia di gudang kami</p>
            </div>
            <a href="{{ route('shop.catalog') }}" class="btn btn-outline-shop px-4">
                Lihat Semua <i class="fa-solid fa-arrow-right ms-2"></i>
            </a>
        </div>

        <div class="row g-4">
            @forelse($featured as $barang)
            <div class="col-6 col-md-4 col-lg-3">
                <div class="product-card card h-100">
                    <a href="{{ route('shop.product', $barang->id_barang) }}">
                        <img src="https://placehold.co/400x300/e2e8f0/64748b?text={{ urlencode($barang->kode_barang) }}"
                             alt="{{ $barang->nama_barang }}" loading="lazy">
                    </a>
                    <div class="card-body d-flex flex-column">
                        <p class="text-muted small mb-1">{{ $barang->kode_barang }}</p>
                        <h6 class="fw-700 mb-1 flex-grow-1">
                            <a href="{{ route('shop.product', $barang->id_barang) }}"
                               class="text-dark text-decoration-none stretched-link-inner">
                               {{ $barang->nama_barang }}
                            </a>
                        </h6>
                        <div class="price mb-2">
                            Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}
                        </div>
                        <div class="d-flex align-items-center justify-content-between">
                            @if($barang->stok > 5)
                                <span class="badge badge-stok-ok">Stok: {{ $barang->stok }}</span>
                            @elseif($barang->stok > 0)
                                <span class="badge badge-stok-low">Sisa: {{ $barang->stok }}</span>
                            @else
                                <span class="badge badge-stok-out">Habis</span>
                            @endif
                            <a href="{{ route('shop.product', $barang->id_barang) }}"
                               class="btn btn-sm btn-primary-shop px-3">
                                Beli
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="col-12 text-center py-5">
                <i class="fa-solid fa-box-open fa-3x text-muted mb-3"></i>
                <p class="text-muted">Belum ada produk tersedia.</p>
            </div>
            @endforelse
        </div>
    </div>
</section>

{{-- ══ WHY US ══════════════════════════════════════════════════════════════ --}}
<section class="container">
    <div class="why-us">
        <div class="row g-4 text-center">
            <div class="col-6 col-md-3 why-item">
                <div class="why-icon"><i class="fa-solid fa-shield-halved"></i></div>
                <h6>Produk Original</h6>
                <p>Garansi keaslian setiap produk yang kami jual.</p>
            </div>
            <div class="col-6 col-md-3 why-item">
                <div class="why-icon"><i class="fa-solid fa-truck-fast"></i></div>
                <h6>Gratis Ongkir</h6>
                <p>Ongkos kirim gratis untuk setiap pembelian.</p>
            </div>
            <div class="col-6 col-md-3 why-item">
                <div class="why-icon"><i class="fa-solid fa-rotate-left"></i></div>
                <h6>Mudah Dikembalikan</h6>
                <p>Proses pengembalian barang yang mudah dan cepat.</p>
            </div>
            <div class="col-6 col-md-3 why-item">
                <div class="why-icon"><i class="fa-solid fa-headset"></i></div>
                <h6>Support 24/7</h6>
                <p>Tim kami siap membantu kapanpun Anda butuhkan.</p>
            </div>
        </div>
    </div>
</section>

@endsection
