<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="@yield('meta_description', 'Toko Sparepart Motor Online — Temukan sparepart berkualitas dengan harga terbaik.')">
    <title>@yield('title', 'Toko Sparepart') — MotorParts Store</title>

    {{-- Bootstrap 5 --}}
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary:   #E63946;
            --primary-d: #c1121f;
            --dark:      #1a1a2e;
            --dark2:     #16213e;
            --accent:    #f4a261;
            --light-bg:  #f8f9fa;
            --card-bg:   #ffffff;
        }
        * { font-family: 'Inter', sans-serif; }
        body { background: var(--light-bg); color: #1a1a2e; }

        /* ── NAVBAR ─────────────────────────────────────────── */
        .shop-navbar {
            background: linear-gradient(135deg, var(--dark) 0%, var(--dark2) 100%);
            box-shadow: 0 2px 20px rgba(0,0,0,.35);
            padding: .75rem 0;
        }
        .shop-navbar .navbar-brand {
            font-size: 1.35rem;
            font-weight: 800;
            letter-spacing: -.5px;
            color: #fff !important;
        }
        .shop-navbar .navbar-brand span { color: var(--primary); }
        .shop-navbar .nav-link {
            color: rgba(255,255,255,.82) !important;
            font-weight: 500;
            padding: .45rem .9rem !important;
            border-radius: 6px;
            transition: all .2s;
        }
        .shop-navbar .nav-link:hover,
        .shop-navbar .nav-link.active {
            color: #fff !important;
            background: rgba(255,255,255,.12);
        }
        .cart-btn {
            background: var(--primary);
            color: #fff !important;
            border-radius: 8px !important;
            padding: .45rem 1rem !important;
        }
        .cart-btn:hover { background: var(--primary-d) !important; }
        .cart-badge {
            background: var(--accent);
            color: #fff;
            font-size: .68rem;
            font-weight: 700;
            min-width: 18px;
            height: 18px;
            border-radius: 9px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 4px;
            margin-left: 4px;
            vertical-align: middle;
        }
        .navbar-toggler { border-color: rgba(255,255,255,.3); }
        .navbar-toggler-icon { filter: invert(1); }
        
        /* ── OFFCANVAS MOBILE ───────────────────────────────── */
        .offcanvas-shop {
            background: linear-gradient(180deg, var(--dark) 0%, #0d1117 100%);
            max-width: 300px;
        }
        .offcanvas-shop .offcanvas-header {
            border-bottom: 1px solid rgba(255,255,255,.08);
            padding: 1.2rem 1.5rem;
        }
        .offcanvas-shop .offcanvas-body { padding: 1rem 0; }
        .offcanvas-shop .nav-link {
            padding: .75rem 1.5rem !important;
            border-radius: 0 !important;
            font-size: .95rem;
            border-left: 3px solid transparent;
        }
        .offcanvas-shop .nav-link:hover,
        .offcanvas-shop .nav-link.active {
            background: rgba(230,57,70,.1) !important;
            border-left-color: var(--primary);
        }
        .offcanvas-shop .nav-divider {
            border-top: 1px solid rgba(255,255,255,.08);
            margin: .5rem 1.5rem;
        }
        .offcanvas-shop .nav-section-label {
            color: rgba(255,255,255,.35);
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 1px;
            padding: .8rem 1.5rem .3rem;
        }
        
        /* ── CUSTOMER ACCOUNT LINK ─────────────────────────── */

        /* ── FLASH MESSAGES ─────────────────────────────────── */
        .flash-wrap { position: sticky; top: 0; z-index: 1050; }
        .alert { border: none; border-radius: 0; margin-bottom: 0; font-size: .9rem; }

        /* ── GENERAL CARDS ──────────────────────────────────── */
        .product-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 2px 12px rgba(0,0,0,.07);
            transition: transform .25s, box-shadow .25s;
        }
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 32px rgba(0,0,0,.13);
        }
        .product-card img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: #eee;
        }
        .product-card .card-body { padding: 1.1rem 1.2rem; }
        .product-card .price {
            font-size: 1.15rem;
            font-weight: 700;
            color: var(--primary);
        }
        .badge-stok-ok  { background: #d1fae5; color: #065f46; font-size: .75rem; }
        .badge-stok-low { background: #fef3c7; color: #92400e; font-size: .75rem; }
        .badge-stok-out { background: #fee2e2; color: #991b1b; font-size: .75rem; }

        /* ── BTN STYLES ─────────────────────────────────────── */
        .btn-primary-shop {
            background: var(--primary);
            border: none;
            color: #fff;
            border-radius: 8px;
            font-weight: 600;
            transition: background .2s, transform .15s;
        }
        .btn-primary-shop:hover {
            background: var(--primary-d);
            color: #fff;
            transform: translateY(-1px);
        }
        .btn-outline-shop {
            border: 2px solid var(--primary);
            color: var(--primary);
            border-radius: 8px;
            font-weight: 600;
            background: transparent;
            transition: all .2s;
        }
        .btn-outline-shop:hover {
            background: var(--primary);
            color: #fff;
        }

        /* ── FOOTER ─────────────────────────────────────────── */
        .shop-footer {
            background: var(--dark);
            color: rgba(255,255,255,.75);
            padding: 3rem 0 1.5rem;
            margin-top: 5rem;
        }
        .shop-footer h6 { color: #fff; font-weight: 700; margin-bottom: 1rem; }
        .shop-footer a { color: rgba(255,255,255,.6); text-decoration: none; display: block; margin-bottom: .4rem; font-size: .9rem; }
        .shop-footer a:hover { color: var(--primary); }
        .shop-footer .footer-brand { font-size: 1.3rem; font-weight: 800; color: #fff; }
        .shop-footer .footer-brand span { color: var(--primary); }
        .footer-divider { border-color: rgba(255,255,255,.1); margin: 1.5rem 0; }
        .footer-bottom { font-size: .82rem; color: rgba(255,255,255,.45); }

        @stack('styles_inline')
    </style>
    @stack('styles')
</head>
<body>

{{-- ══ NAVBAR ══════════════════════════════════════════════════════════════ --}}
<nav class="shop-navbar navbar navbar-expand-lg sticky-top">
    <div class="container">
        {{-- Brand --}}
        <a class="navbar-brand" href="{{ route('shop.home') }}">
            <i class="fa-solid fa-motorcycle me-2"></i>Motor<span>Parts</span>
        </a>

        {{-- Mobile: Cart icon + Hamburger --}}
        <div class="d-flex align-items-center gap-2 d-lg-none">
            @php $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
            <a href="{{ route('shop.cart') }}" class="cart-btn nav-link position-relative">
                <i class="fa-solid fa-cart-shopping"></i>
                @if($cartCount > 0)
                    <span class="cart-badge">{{ $cartCount }}</span>
                @endif
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="offcanvas" data-bs-target="#shopOffcanvas">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>

        {{-- Offcanvas (Mobile) / Collapse (Desktop) --}}
        <div class="offcanvas offcanvas-start offcanvas-shop d-lg-none" tabindex="-1" id="shopOffcanvas">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title text-white fw-bold">
                    <i class="fa-solid fa-motorcycle me-2" style="color:var(--primary)"></i>Motor<span style="color:var(--primary)">Parts</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"></button>
            </div>
            <div class="offcanvas-body">
                <div class="nav-section-label">Menu</div>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop.home') ? 'active' : '' }}" href="{{ route('shop.home') }}">
                            <i class="fa-solid fa-house-chimney fa-fw me-2"></i>Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop.catalog') ? 'active' : '' }}" href="{{ route('shop.catalog') }}">
                            <i class="fa-solid fa-th-large fa-fw me-2"></i>Produk
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('shop.cart') ? 'active' : '' }}" href="{{ route('shop.cart') }}">
                            <i class="fa-solid fa-cart-shopping fa-fw me-2"></i>Keranjang
                            @if($cartCount > 0)
                                <span class="cart-badge">{{ $cartCount }}</span>
                            @endif
                        </a>
                    </li>
                </ul>
                
                <div class="nav-divider"></div>

                @if(session()->has('customer_id'))
                    <div class="nav-section-label">Akun</div>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('shop.orders.*') ? 'active' : '' }}" href="{{ route('shop.orders.index') }}">
                                <i class="fa-solid fa-box-open fa-fw me-2"></i>Pesanan Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link {{ request()->routeIs('shop.account.settings') ? 'active' : '' }}" href="{{ route('shop.account.settings') }}">
                                <i class="fa-solid fa-gear fa-fw me-2"></i>Pengaturan
                            </a>
                        </li>
                    </ul>
                    <div class="nav-divider"></div>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <form action="{{ route('customer.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link w-100 text-start border-0 bg-transparent" style="color: #ef4444 !important;">
                                    <i class="fa-solid fa-right-from-bracket fa-fw me-2"></i>Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                @else
                    <div class="nav-section-label">Akun</div>
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.login') }}">
                                <i class="fa-solid fa-right-to-bracket fa-fw me-2"></i>Sign In
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('customer.register') }}">
                                <i class="fa-solid fa-user-plus fa-fw me-2"></i>Sign Up
                            </a>
                        </li>
                    </ul>
                @endif
            </div>
        </div>

        {{-- Desktop Navbar --}}
        <div class="collapse navbar-collapse d-none d-lg-flex">
            @php if(!isset($cartCount)) $cartCount = collect(session('cart', []))->sum('quantity'); @endphp
            <ul class="navbar-nav me-auto mb-0 gap-1">
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop.home') ? 'active' : '' }}" href="{{ route('shop.home') }}">
                        <i class="fa-solid fa-house-chimney me-1"></i> Home
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop.catalog') ? 'active' : '' }}" href="{{ route('shop.catalog') }}">
                        <i class="fa-solid fa-th-large me-1"></i> Katalog
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop.cart') ? 'active' : '' }}" href="{{ route('shop.cart') }}">
                        <i class="fa-solid fa-cart-shopping me-1"></i> Keranjang
                        @if($cartCount > 0)
                            <span class="cart-badge">{{ $cartCount }}</span>
                        @endif
                    </a>
                </li>
            </ul>

            {{-- Right side: Account --}}
            <ul class="navbar-nav align-items-center gap-1">
                @if(session()->has('customer_id'))
                    <li class="nav-item">
                        <a class="nav-link d-flex align-items-center gap-2 {{ request()->routeIs('shop.account.settings') ? 'active' : '' }}" href="{{ route('shop.account.settings') }}">
                            <i class="fa-solid fa-circle-user"></i>
                            {{ session('customer_name') }}
                        </a>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('customer.login') }}">
                            <i class="fa-solid fa-right-to-bracket me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item ms-1">
                        <a class="nav-link btn btn-outline-light btn-sm px-3" href="{{ route('customer.register') }}">
                            Daftar
                        </a>
                    </li>
                @endif
            </ul>
        </div>
    </div>
</nav>

{{-- ══ FLASH MESSAGES ══════════════════════════════════════════════════════ --}}
<div class="flash-wrap">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show py-2">
            <div class="container d-flex align-items-center gap-2">
                <i class="fa-solid fa-circle-check"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show py-2">
            <div class="container d-flex align-items-center gap-2">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif
</div>

{{-- ══ MAIN CONTENT ════════════════════════════════════════════════════════ --}}
<main>
    @yield('content')
</main>

{{-- ══ FOOTER ══════════════════════════════════════════════════════════════ --}}
<footer class="shop-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="footer-brand mb-2">
                    <i class="fa-solid fa-motorcycle me-2"></i>Motor<span>Parts</span>
                </div>
                <p class="small" style="color:rgba(255,255,255,.55)">
                    Toko sparepart motor terpercaya.<br>
                    Kualitas terjamin, harga bersaing.
                </p>
                <div class="d-flex gap-3 mt-3">
                    <a href="#" style="font-size:1.2rem"><i class="fab fa-instagram"></i></a>
                    <a href="#" style="font-size:1.2rem"><i class="fab fa-whatsapp"></i></a>
                    <a href="#" style="font-size:1.2rem"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
            <div class="col-md-2">
                <h6>Toko</h6>
                <a href="{{ route('shop.home') }}">Home</a>
                <a href="{{ route('shop.catalog') }}">Katalog Produk</a>
                <a href="{{ route('shop.cart') }}">Keranjang</a>
            </div>
            <div class="col-md-3">
                <h6>Akun</h6>
                @if(session()->has('customer_id'))
                    <a href="{{ route('shop.orders.index') }}">Pesanan Saya</a>
                    <a href="{{ route('shop.account.settings') }}">Pengaturan Akun</a>
                @else
                    <a href="{{ route('customer.login') }}">Login Pelanggan</a>
                    <a href="{{ route('customer.register') }}">Daftar Akun</a>
                @endif
                <hr style="border-color: rgba(255,255,255,0.2); margin: 0.8rem 0;">
                <a href="{{ route('login') }}" class="text-warning"><i class="fa-solid fa-user-shield me-2"></i>Portal Pegawai</a>
            </div>
            <div class="col-md-3">
                <h6>Kontak</h6>
                <p class="small mb-1"><i class="fa-solid fa-location-dot me-2"></i>Jl. Motor Raya No. 99</p>
                <p class="small mb-1"><i class="fa-solid fa-phone me-2"></i>+62 812-3456-7890</p>
                <p class="small"><i class="fa-solid fa-envelope me-2"></i>info@motorparts.id</p>
            </div>
        </div>
        <hr class="footer-divider">
        <p class="footer-bottom text-center mb-0">
            &copy; {{ date('Y') }} MotorParts Store. Hak cipta dilindungi.
        </p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
