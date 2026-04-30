<!DOCTYPE html>
<html lang="id">
<head>
    <title>@yield('title', 'Sistem Inventory') - Sparepart Motor</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    @stack('styles')
</head>
<body class="bg-light">

{{-- NAVBAR (Pengganti header.php) --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4 shadow">
  <div class="container">
    <a class="navbar-brand fw-bold" href="#">
        <i class="fa-solid fa-motorcycle me-2"></i>Bengkel Motor
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>

    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav me-auto mb-2 mb-lg-0">

        {{-- Dashboard Link sesuai role --}}
        <li class="nav-item">
            @php
                $dashRoute = match(session('role')) {
                    'kasir' => 'dashboard.kasir',
                    'owner' => 'dashboard.owner',
                    default => 'dashboard.admin',
                };
            @endphp
            <a class="nav-link" href="{{ route($dashRoute) }}">Dashboard</a>
        </li>

        {{-- Menu Admin --}}
        @if(session('role') == 'admin')
            <li class="nav-item dropdown">
                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                    Data Master
                </a>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="{{ route('barang.index') }}">Data Barang (Edit)</a></li>
                    <li><a class="dropdown-item" href="{{ route('supplier.index') }}">Data Supplier</a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="{{ route('user.index') }}">Data User</a></li>
                </ul>
            </li>
            <li class="nav-item"><a class="nav-link" href="{{ route('barang-masuk.index') }}">Barang Masuk</a></li>
            <li class="nav-item"><a class="nav-link" href="{{ route('barang-keluar.index') }}">Barang Keluar</a></li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('admin.orders.index') }}">
                    <i class="fa-solid fa-cart-shopping me-1"></i>Pesanan Online
                </a>
            </li>
        @endif

        {{-- Menu Kasir --}}
        @if(session('role') == 'kasir')
            <li class="nav-item">
                <a class="nav-link" href="{{ route('barang-keluar.kasir') }}">Transaksi Penjualan</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('barang.kasir') }}">Cek Gudang</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="{{ route('barang-masuk.create') }}">Input Barang Masuk</a>
            </li>
        @endif

        {{-- Menu Owner --}}
        @if(session('role') == 'owner')
            <li class="nav-item"><a class="nav-link" href="{{ route('barang-keluar.index') }}">Laporan Keuangan</a></li>
        @endif

      </ul>

      <span class="navbar-text text-white me-3">
        Halo, <b>{{ session('nama_lengkap') }}</b> ({{ ucfirst(session('role')) }})
      </span>
      <form action="{{ route('logout') }}" method="POST" class="d-inline">
          @csrf
          <button type="submit" class="btn btn-danger btn-sm">Logout</button>
      </form>
    </div>
  </div>
</nav>

{{-- CONTENT AREA --}}
<div class="container p-4 bg-white rounded shadow-sm" style="min-height: 400px;">

    {{-- Flash Messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @yield('content')
</div>

{{-- FOOTER (Pengganti footer.php) --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
@stack('scripts')
</body>
</html>
