@extends('layouts.shop')
@section('title', 'Dashboard Akun — MotorParts Store')

@push('styles')
<style>
    .account-wrapper {
        padding: 3rem 0;
        min-height: 70vh;
    }
    .sidebar-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,.04);
        overflow: hidden;
        border: none;
    }
    .sidebar-header {
        background: linear-gradient(135deg, var(--dark), var(--dark2));
        color: #fff;
        padding: 1.5rem 1rem;
        text-align: center;
    }
    .sidebar-header .avatar {
        width: 64px;
        height: 64px;
        background: rgba(255,255,255,.15);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        margin-bottom: 1rem;
    }
    .sidebar-menu {
        padding: 1rem 0;
    }
    .sidebar-menu .nav-link {
        color: #4a4a5a;
        font-weight: 500;
        padding: .85rem 1.5rem;
        border-left: 3px solid transparent;
        border-radius: 0;
        transition: all .2s;
    }
    .sidebar-menu .nav-link:hover {
        background: var(--light-bg);
        color: var(--primary);
    }
    .sidebar-menu .nav-link.active {
        background: rgba(230,57,70,.08);
        color: var(--primary);
        border-left-color: var(--primary);
    }
    
    .content-card {
        background: #fff;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0,0,0,.04);
        border: none;
    }
    .content-header {
        padding: 1.5rem 2rem;
        border-bottom: 1px solid #f0f0f0;
    }
    .content-header h5 {
        font-weight: 700;
        margin: 0;
        color: var(--dark);
    }
    .content-body {
        padding: 2rem;
    }
    
    .form-control {
        border-radius: 8px;
        padding: .6rem 1rem;
    }
    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(230,57,70,.15);
    }
    .btn-save {
        background: var(--primary);
        color: #fff;
        font-weight: 600;
        padding: .7rem 2rem;
        border-radius: 8px;
        border: none;
        transition: all .2s;
    }
    .btn-save:hover {
        background: var(--primary-d);
        transform: translateY(-2px);
    }
</style>
@endpush

@section('content')
<div class="container account-wrapper">
    <div class="row g-4">
        {{-- ── SIDEBAR ── --}}
        <div class="col-lg-3">
            <div class="sidebar-card h-100">
                <div class="sidebar-header">
                    <div class="avatar">
                        <i class="fa-solid fa-user"></i>
                    </div>
                    <h6 class="mb-1 fw-bold">{{ $customer->name }}</h6>
                    <small class="opacity-75">{{ $customer->email }}</small>
                </div>
                
                <div class="sidebar-menu nav flex-column nav-pills" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                    <button class="nav-link text-start active" id="v-pills-profil-tab" data-bs-toggle="pill" data-bs-target="#v-pills-profil" type="button" role="tab">
                        <i class="fa-solid fa-user fa-fw me-2"></i> Profil Pengguna
                    </button>
                    <button class="nav-link text-start" id="v-pills-alamat-tab" data-bs-toggle="pill" data-bs-target="#v-pills-alamat" type="button" role="tab">
                        <i class="fa-solid fa-map-location-dot fa-fw me-2"></i> Buku Alamat
                    </button>
                    <a class="nav-link text-start" href="{{ route('shop.orders.index') }}">
                        <i class="fa-solid fa-box-open fa-fw me-2"></i> Pesanan Saya
                    </a>
                    <button class="nav-link text-start" id="v-pills-keamanan-tab" data-bs-toggle="pill" data-bs-target="#v-pills-keamanan" type="button" role="tab">
                        <i class="fa-solid fa-lock fa-fw me-2"></i> Keamanan
                    </button>
                    
                    <hr class="my-2 border-secondary opacity-25 mx-3">
                    
                    <form action="{{ route('customer.logout') }}" method="POST" class="m-0 p-0">
                        @csrf
                        <button type="submit" class="nav-link text-start text-danger fw-bold w-100" style="border-left-color:transparent;">
                            <i class="fa-solid fa-power-off fa-fw me-2"></i> Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- ── CONTENT ── --}}
        <div class="col-lg-9">
            <form action="{{ route('shop.account.settings.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="content-card h-100">
                    <div class="tab-content" id="v-pills-tabContent">
                        
                        {{-- TAB PROFIL --}}
                        <div class="tab-pane fade show active" id="v-pills-profil" role="tabpanel">
                            <div class="content-header">
                                <h5>Profil Pengguna</h5>
                            </div>
                            <div class="content-body">
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Nama Lengkap</label>
                                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $customer->name) }}">
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Email (Tidak bisa diubah)</label>
                                        <input type="email" class="form-control bg-light" value="{{ $customer->email }}" disabled>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Nomor Telepon/WA</label>
                                        <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $customer->phone) }}">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB ALAMAT --}}
                        <div class="tab-pane fade" id="v-pills-alamat" role="tabpanel">
                            <div class="content-header">
                                <h5>Buku Alamat</h5>
                            </div>
                            <div class="content-body">
                                <div class="row g-4">
                                    <div class="col-12">
                                        <label class="form-label text-muted small fw-bold">Alamat Lengkap (Jalan, RT/RW, dsb.)</label>
                                        <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $customer->address) }}</textarea>
                                        @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label text-muted small fw-bold">Kota/Kabupaten</label>
                                        <input type="text" name="city" class="form-control @error('city') is-invalid @enderror" value="{{ old('city', $customer->city) }}">
                                        @error('city')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-5">
                                        <label class="form-label text-muted small fw-bold">Provinsi</label>
                                        <input type="text" name="province" class="form-control @error('province') is-invalid @enderror" value="{{ old('province', $customer->province) }}">
                                        @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-3">
                                        <label class="form-label text-muted small fw-bold">Kode Pos</label>
                                        <input type="text" name="postal_code" class="form-control @error('postal_code') is-invalid @enderror" value="{{ old('postal_code', $customer->postal_code) }}">
                                        @error('postal_code')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB KEAMANAN --}}
                        <div class="tab-pane fade" id="v-pills-keamanan" role="tabpanel">
                            <div class="content-header">
                                <h5>Keamanan & Password</h5>
                            </div>
                            <div class="content-body">
                                <p class="text-muted small mb-4">Kosongkan kolom di bawah jika Anda tidak ingin mengubah password saat ini.</p>
                                <div class="row g-4">
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Password Baru</label>
                                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label text-muted small fw-bold">Konfirmasi Password Baru</label>
                                        <input type="password" name="password_confirmation" class="form-control" placeholder="Ulangi password baru">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                    
                    {{-- TOMBOL SIMPAN GLOBAL --}}
                    <div class="content-header border-bottom-0 border-top mt-auto bg-light" style="border-radius:0 0 12px 12px;">
                        <div class="text-end">
                            <button type="submit" class="btn-save"><i class="fa-solid fa-floppy-disk me-2"></i>Simpan Semua Perubahan</button>
                        </div>
                    </div>
                    
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
