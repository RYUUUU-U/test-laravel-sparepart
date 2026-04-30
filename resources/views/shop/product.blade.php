@extends('layouts.shop')

@section('title', $barang->nama_barang . ' — MotorParts Store')

@push('styles')
<style>
    .product-detail-img {
        border-radius: 16px;
        width: 100%;
        aspect-ratio: 4/3;
        object-fit: cover;
        box-shadow: 0 8px 32px rgba(0,0,0,.12);
    }
    .product-detail-card {
        border: none;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 4px 24px rgba(0,0,0,.09);
    }
    .product-code { font-size: .82rem; color: #9ca3af; font-weight: 600; letter-spacing: .5px; }
    .product-title { font-size: 1.75rem; font-weight: 800; color: #1a1a2e; margin: .4rem 0; }
    .product-price { font-size: 2rem; font-weight: 800; color: #E63946; }
    .meta-row { display: flex; gap: 1rem; flex-wrap: wrap; margin: 1rem 0; }
    .meta-pill {
        background: #f3f4f6;
        border-radius: 8px;
        padding: .35rem .9rem;
        font-size: .85rem;
        font-weight: 600;
        color: #374151;
    }
    .meta-pill i { color: #E63946; margin-right: .4rem; }
    .qty-control { display: flex; align-items: center; border: 1.5px solid #e5e7eb; border-radius: 10px; overflow: hidden; }
    .qty-control button {
        background: #f3f4f6;
        border: none;
        width: 44px; height: 48px;
        font-size: 1.1rem;
        cursor: pointer;
        transition: background .15s;
    }
    .qty-control button:hover { background: #e5e7eb; }
    .qty-control input {
        width: 64px; height: 48px;
        border: none; outline: none;
        text-align: center;
        font-size: 1.1rem;
        font-weight: 700;
    }
    .related-card { border-radius: 14px; border: none; box-shadow: 0 2px 12px rgba(0,0,0,.07); }
    .related-card:hover { transform: translateY(-3px); box-shadow: 0 8px 24px rgba(0,0,0,.12); transition: all .2s; }
</style>
@endpush

@section('content')
<div class="container py-5">

    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('shop.home') }}" class="text-decoration-none">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.catalog') }}" class="text-decoration-none">Katalog</a></li>
            <li class="breadcrumb-item active">{{ $barang->nama_barang }}</li>
        </ol>
    </nav>

    <div class="row g-5 align-items-start">
        {{-- ── Gambar ───────────────────────────────────────── --}}
        <div class="col-md-5">
            <img src="https://placehold.co/600x450/e2e8f0/64748b?text={{ urlencode($barang->kode_barang) }}"
                 alt="{{ $barang->nama_barang }}" class="product-detail-img">
            <div class="text-center mt-3">
                <span class="badge bg-light text-muted border me-2 p-2">
                    <i class="fa-solid fa-tag me-1 text-danger"></i>{{ $barang->kode_barang }}
                </span>
                <span class="badge bg-light text-muted border p-2">
                    <i class="fa-solid fa-folder me-1 text-danger"></i>{{ $barang->kategori }}
                </span>
            </div>
        </div>

        {{-- ── Detail & Add to Cart ─────────────────────────── --}}
        <div class="col-md-7">
            <div class="product-detail-card">
                <p class="product-code">SKU: {{ $barang->kode_barang }}</p>
                <h1 class="product-title">{{ $barang->nama_barang }}</h1>
                <div class="product-price">Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}</div>

                <div class="meta-row">
                    <div class="meta-pill">
                        <i class="fa-solid fa-layer-group"></i>Kategori: {{ $barang->kategori }}
                    </div>
                    <div class="meta-pill">
                        <i class="fa-solid fa-ruler"></i>Satuan: {{ $barang->satuan }}
                    </div>
                    @if($barang->stok > 5)
                        <div class="meta-pill" style="background:#d1fae5;color:#065f46">
                            <i class="fa-solid fa-circle-check" style="color:#059669"></i>
                            Stok Tersedia ({{ $barang->stok }})
                        </div>
                    @elseif($barang->stok > 0)
                        <div class="meta-pill" style="background:#fef3c7;color:#92400e">
                            <i class="fa-solid fa-triangle-exclamation" style="color:#d97706"></i>
                            Stok Terbatas ({{ $barang->stok }})
                        </div>
                    @else
                        <div class="meta-pill" style="background:#fee2e2;color:#991b1b">
                            <i class="fa-solid fa-ban" style="color:#dc2626"></i>Stok Habis
                        </div>
                    @endif
                </div>

                <hr class="my-3">

                @if($barang->stok > 0)
                    <form action="{{ route('shop.cart.add') }}" method="POST">
                        @csrf
                        <input type="hidden" name="barang_id" value="{{ $barang->id_barang }}">

                        <label class="form-label fw-600 mb-2">Jumlah</label>
                        <div class="d-flex align-items-center gap-3 mb-4">
                            <div class="qty-control">
                                <button type="button" onclick="changeQty(-1)">−</button>
                                <input type="number" name="quantity" id="qtyInput" value="1"
                                       min="1" max="{{ $barang->stok }}">
                                <button type="button" onclick="changeQty(1)">+</button>
                            </div>
                            <span class="text-muted small">Maks. {{ $barang->stok }} {{ $barang->satuan }}</span>
                        </div>

                        {{-- Total preview --}}
                        <div class="alert alert-light border mb-3 py-2 px-3">
                            <span class="small text-muted">Total: </span>
                            <strong id="totalPreview" class="text-danger">
                                Rp {{ number_format($barang->harga_jual, 0, ',', '.') }}
                            </strong>
                        </div>

                        <div class="d-flex gap-3 flex-wrap">
                            <button type="submit" class="btn btn-primary-shop btn-lg flex-grow-1">
                                <i class="fa-solid fa-cart-plus me-2"></i>Tambah ke Keranjang
                            </button>
                            <a href="{{ route('shop.cart') }}" class="btn btn-outline-shop btn-lg">
                                <i class="fa-solid fa-bag-shopping"></i>
                            </a>
                        </div>
                    </form>
                @else
                    <div class="alert alert-danger">
                        <i class="fa-solid fa-ban me-2"></i>Maaf, produk ini sedang tidak tersedia.
                    </div>
                    <a href="{{ route('shop.catalog') }}" class="btn btn-primary-shop btn-lg w-100">
                        Lihat Produk Lain
                    </a>
                @endif
            </div>
        </div>
    </div>

    {{-- ── Related Products ─────────────────────────────────────── --}}
    @if($related->count())
    <div class="mt-5">
        <h4 class="fw-800 mb-4">Produk Terkait</h4>
        <div class="row g-4">
            @foreach($related as $r)
            <div class="col-6 col-md-3">
                <div class="related-card card h-100 overflow-hidden">
                    <a href="{{ route('shop.product', $r->id_barang) }}">
                        <img src="https://placehold.co/400x300/e2e8f0/64748b?text={{ urlencode($r->kode_barang) }}"
                             alt="{{ $r->nama_barang }}"
                             style="width:100%;height:160px;object-fit:cover">
                    </a>
                    <div class="card-body p-3">
                        <p class="small text-muted mb-1">{{ $r->kode_barang }}</p>
                        <a href="{{ route('shop.product', $r->id_barang) }}"
                           class="fw-600 text-dark text-decoration-none small d-block mb-2">
                           {{ $r->nama_barang }}
                        </a>
                        <div class="text-danger fw-700">Rp {{ number_format($r->harga_jual, 0, ',', '.') }}</div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
    const price = {{ (int) $barang->harga_jual }};
    const maxQty = {{ (int) $barang->stok }};
    const input = document.getElementById('qtyInput');
    const totalEl = document.getElementById('totalPreview');

    function changeQty(delta) {
        let val = parseInt(input.value) + delta;
        val = Math.max(1, Math.min(maxQty, val));
        input.value = val;
        updateTotal();
    }

    input?.addEventListener('input', updateTotal);

    function updateTotal() {
        let qty = Math.max(1, Math.min(maxQty, parseInt(input.value) || 1));
        input.value = qty;
        const total = price * qty;
        if (totalEl) totalEl.textContent = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endpush
