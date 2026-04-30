@extends('layouts.shop')
@section('title', 'Keranjang Belanja — MotorParts Store')

@push('styles')
<style>
    .cart-wrapper { background: #fff; border-radius: 20px; box-shadow: 0 4px 24px rgba(0,0,0,.08); overflow: hidden; }
    .cart-header { background: linear-gradient(135deg,#1a1a2e,#16213e); color:#fff; padding:1.5rem 2rem; }
    .cart-header h4 { font-weight:800; margin:0; }
    .cart-body { padding: 1.5rem 2rem; }

    .cart-item {
        display: flex; gap: 1.2rem; align-items: center;
        padding: 1.2rem 0;
        border-bottom: 1px solid #f3f4f6;
    }
    .cart-item:last-child { border-bottom: none; }
    .cart-item-img {
        width: 80px; height: 70px; border-radius: 10px;
        object-fit: cover; flex-shrink: 0;
        background: #f3f4f6;
    }
    .cart-item-name { font-weight: 700; color: #1a1a2e; font-size: .95rem; }
    .cart-item-code { font-size: .78rem; color: #9ca3af; }
    .cart-item-price { color: #E63946; font-weight: 700; font-size: .95rem; }

    .qty-mini { display: flex; align-items: center; gap: .3rem; }
    .qty-mini input {
        width: 52px; text-align: center;
        border: 1.5px solid #e5e7eb;
        border-radius: 8px; padding: .3rem;
        font-weight: 700; font-size: .9rem;
    }
    .qty-mini input:focus { outline: none; border-color: #E63946; }

    .summary-card {
        background: #fff;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,.08);
        padding: 1.8rem;
        position: sticky; top: 80px;
    }
    .summary-card h5 { font-weight: 800; margin-bottom: 1.2rem; }
    .summary-row { display: flex; justify-content: space-between; margin-bottom: .7rem; font-size: .92rem; }
    .summary-total {
        display: flex; justify-content: space-between;
        font-size: 1.15rem; font-weight: 800;
        color: #E63946; margin-top: .5rem; padding-top: .8rem;
        border-top: 2px solid #f3f4f6;
    }

    .empty-cart { text-align: center; padding: 4rem 2rem; }
    .empty-cart i { font-size: 5rem; color: #e5e7eb; display: block; margin-bottom: 1.5rem; }
    .empty-cart h5 { color: #6b7280; font-weight: 600; }
</style>
@endpush

@section('content')
<div class="container py-5">
    <h2 class="fw-800 mb-4"><i class="fa-solid fa-cart-shopping me-2 text-danger"></i>Keranjang Belanja</h2>

    @if(empty($cart))

    {{-- ── Empty state ─────────────────────────────────────────── --}}
    <div class="cart-wrapper">
        <div class="empty-cart">
            <i class="fa-solid fa-cart-shopping"></i>
            <h5>Keranjang Anda Masih Kosong</h5>
            <p class="text-muted small mb-4">Temukan produk sparepart yang Anda butuhkan di katalog kami.</p>
            <a href="{{ route('shop.catalog') }}" class="btn btn-primary-shop btn-lg px-5">
                <i class="fa-solid fa-th-large me-2"></i>Mulai Belanja
            </a>
        </div>
    </div>

    @else

    <div class="row g-4 align-items-start">

        {{-- ── Cart Items ─────────────────────────────────────────── --}}
        <div class="col-lg-8">
            <div class="cart-wrapper">
                <div class="cart-header d-flex justify-content-between align-items-center">
                    <h4><i class="fa-solid fa-box me-2"></i>{{ count($cart) }} Item</h4>
                    <form action="{{ route('shop.cart.clear') }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-light"
                                onclick="return confirm('Kosongkan semua keranjang?')">
                            <i class="fa-solid fa-trash me-1"></i>Kosongkan
                        </button>
                    </form>
                </div>
                <div class="cart-body">
                    @foreach($cart as $id => $item)
                    <div class="cart-item">
                        {{-- Thumbnail --}}
                        @php
                            $cartImgSrc = !empty($item['image_url']) ? asset('storage/' . $item['image_url']) : null;
                            $cartFallback = 'https://placehold.co/160x140/e2e8f0/64748b?text=' . urlencode($item['code']);
                        @endphp
                        <img src="{{ $cartImgSrc ?? $cartFallback }}"
                             alt="{{ $item['name'] }}" class="cart-item-img"
                             onerror="this.src='{{ $cartFallback }}'">

                        {{-- Info --}}
                        <div class="flex-grow-1 min-width-0">
                            <div class="cart-item-code">{{ $item['code'] }}</div>
                            <div class="cart-item-name">{{ $item['name'] }}</div>
                            <div class="cart-item-price mt-1">
                                Rp {{ number_format($item['price'], 0, ',', '.') }}<span class="text-muted fw-400 small"> /item</span>
                            </div>
                            {{-- Qty update form --}}
                            <form action="{{ route('shop.cart.update') }}" method="POST" class="d-flex align-items-center gap-2 mt-2">
                                @csrf
                                <input type="hidden" name="barang_id" value="{{ $id }}">
                                <div class="qty-mini">
                                    <input type="number" name="quantity" value="{{ $item['quantity'] }}"
                                           min="1" max="99" class="text-center">
                                </div>
                                <button type="submit" class="btn btn-sm btn-outline-secondary px-2">
                                    <i class="fa-solid fa-rotate me-1"></i>Update
                                </button>
                            </form>
                        </div>

                        {{-- Subtotal + remove --}}
                        <div class="text-end flex-shrink-0">
                            <div class="fw-800 text-dark">
                                Rp {{ number_format($item['subtotal'], 0, ',', '.') }}
                            </div>
                            <form action="{{ route('shop.cart.remove', $id) }}" method="POST" class="mt-2">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-link text-danger p-0"
                                        onclick="return confirm('Hapus item ini?')">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <a href="{{ route('shop.catalog') }}" class="btn btn-outline-shop mt-3">
                <i class="fa-solid fa-arrow-left me-2"></i>Lanjut Belanja
            </a>
        </div>

        {{-- ── Order Summary ─────────────────────────────────────── --}}
        <div class="col-lg-4">
            <div class="summary-card">
                <h5>Ringkasan Pesanan</h5>

                @foreach($cart as $item)
                <div class="summary-row">
                    <span class="text-muted text-truncate me-2" style="max-width:160px">
                        {{ $item['name'] }} <span class="text-muted">×{{ $item['quantity'] }}</span>
                    </span>
                    <span class="fw-600">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</span>
                </div>
                @endforeach

                <div class="summary-row mt-1">
                    <span class="text-muted">Ongkos Kirim</span>
                    <span class="text-success fw-600">Gratis</span>
                </div>

                <div class="summary-total">
                    <span>Total</span>
                    <span>Rp {{ number_format($total, 0, ',', '.') }}</span>
                </div>

                <a href="{{ route('shop.checkout') }}" class="btn btn-primary-shop btn-lg w-100 mt-3">
                    <i class="fa-solid fa-lock me-2"></i>Lanjut Checkout
                </a>
                <p class="text-center text-muted small mt-2 mb-0">
                    <i class="fa-solid fa-shield-halved me-1"></i>Transaksi aman &amp; terenkripsi
                </p>
            </div>
        </div>

    </div>
    @endif
</div>
@endsection
