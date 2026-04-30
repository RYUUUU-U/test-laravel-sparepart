@extends('layouts.shop')
@section('title', 'Daftar Akun — MotorParts Store')

@push('styles')
<style>
    .auth-wrapper { min-height:90vh; display:flex; align-items:center; background:linear-gradient(135deg,#f8f9fa 0%,#fff 100%); padding:3rem 0; }
    .auth-card {
        background:#fff; border-radius:24px;
        box-shadow:0 8px 40px rgba(0,0,0,.1);
        overflow:hidden; max-width:500px; width:100%; margin:0 auto;
    }
    .auth-header {
        background:linear-gradient(135deg,#1a1a2e,#16213e);
        padding:2.2rem 2.5rem 1.8rem; text-align:center; color:#fff;
    }
    .auth-header .brand { font-size:1.4rem; font-weight:800; margin-bottom:.3rem; }
    .auth-header .brand span { color:#E63946; }
    .auth-header h2 { font-size:1.25rem; font-weight:700; margin-bottom:.3rem; }
    .auth-header p { color:rgba(255,255,255,.6); font-size:.88rem; margin:0; }

    .auth-body { padding:2rem 2.5rem; }
    .form-control {
        border:1.5px solid #e5e7eb; border-radius:10px;
        padding:.7rem 1rem; font-size:.9rem;
    }
    .form-control:focus { border-color:#E63946; box-shadow:0 0 0 3px rgba(230,57,70,.1); }
    .form-label { font-weight:600; font-size:.88rem; color:#374151; margin-bottom:.35rem; }
    .input-icon-wrap { position:relative; }
    .input-icon-wrap i { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#9ca3af; }
    .input-icon-wrap .form-control { padding-left:2.6rem; }

    .password-toggle { position:absolute; right:1rem; top:50%; transform:translateY(-50%); cursor:pointer; color:#9ca3af; z-index:5; }

    .btn-auth {
        background:linear-gradient(135deg,#E63946,#c1121f);
        border:none; color:#fff; border-radius:12px;
        padding:.8rem; font-weight:700; font-size:1rem; width:100%;
        transition:transform .15s, box-shadow .15s;
        box-shadow:0 4px 16px rgba(230,57,70,.3);
    }
    .btn-auth:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(230,57,70,.4); color:#fff; }

    .benefit-list { background:#f0fdf4; border:1px solid #a7f3d0; border-radius:12px; padding:1rem; margin-bottom:1.5rem; }
    .benefit-list ul { list-style:none; padding:0; margin:0; }
    .benefit-list li { font-size:.83rem; color:#065f46; padding:.2rem 0; }
    .benefit-list li i { color:#059669; margin-right:.4rem; }

    .auth-footer { text-align:center; margin-top:1.2rem; font-size:.88rem; color:#6b7280; }
    .auth-footer a { color:#E63946; font-weight:600; text-decoration:none; }
    .auth-footer a:hover { text-decoration:underline; }
</style>
@endpush

@section('content')
<div class="auth-wrapper">
    <div class="container">
        <div class="auth-card">

            {{-- Header --}}
            <div class="auth-header">
                <div class="brand"><i class="fa-solid fa-motorcycle me-2"></i>Motor<span>Parts</span></div>
                <h2>Buat Akun Baru</h2>
                <p>Daftar gratis dan mulai belanja sparepart sekarang</p>
            </div>

            {{-- Body --}}
            <div class="auth-body">

                {{-- Benefits --}}
                <div class="benefit-list">
                    <ul>
                        <li><i class="fa-solid fa-check"></i>Akses ke ribuan produk sparepart original</li>
                        <li><i class="fa-solid fa-check"></i>Bebas ongkos kirim untuk semua pesanan</li>
                        <li><i class="fa-solid fa-check"></i>Riwayat pesanan tersimpan otomatis</li>
                    </ul>
                </div>

                @if($errors->any())
                <div class="alert alert-danger py-2 px-3 mb-3" style="border-radius:10px;font-size:.88rem">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first() }}
                </div>
                @endif

                <form action="{{ route('customer.register.process') }}" method="POST">
                    @csrf

                    {{-- Name --}}
                    <div class="mb-3">
                        <label class="form-label" for="name">Nama Lengkap <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fa-solid fa-user"></i>
                            <input type="text" name="name" id="name"
                                   class="form-control @error('name') is-invalid @enderror"
                                   placeholder="Nama lengkap Anda"
                                   value="{{ old('name') }}" autofocus>
                        </div>
                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Email --}}
                    <div class="mb-3">
                        <label class="form-label" for="email">Alamat Email <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="email@example.com"
                                   value="{{ old('email') }}">
                        </div>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Phone --}}
                    <div class="mb-3">
                        <label class="form-label" for="phone">No. Telepon <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fa-solid fa-phone"></i>
                            <input type="text" name="phone" id="phone"
                                   class="form-control @error('phone') is-invalid @enderror"
                                   placeholder="0812-xxxx-xxxx"
                                   value="{{ old('phone') }}" required>
                        </div>
                        @error('phone')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    {{-- Alamat Lengkap --}}
                    <div class="mb-3">
                        <label class="form-label" for="address">Alamat Lengkap <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fa-solid fa-location-dot" style="top:1.3rem"></i>
                            <textarea name="address" id="address" rows="2"
                                      class="form-control @error('address') is-invalid @enderror"
                                      placeholder="Nama jalan, No. rumah, RT/RW, Kelurahan, Kecamatan"
                                      required>{{ old('address') }}</textarea>
                        </div>
                        @error('address')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>

                    <div class="row g-3 mb-3">
                        {{-- Kota --}}
                        <div class="col-md-5">
                            <label class="form-label" for="city">Kota / Kabupaten <span class="text-danger">*</span></label>
                            <input type="text" name="city" id="city"
                                   class="form-control @error('city') is-invalid @enderror"
                                   placeholder="Jakarta Selatan"
                                   value="{{ old('city') }}" required>
                            @error('city')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        {{-- Provinsi --}}
                        <div class="col-md-4">
                            <label class="form-label" for="province">Provinsi <span class="text-danger">*</span></label>
                            <input type="text" name="province" id="province"
                                   class="form-control @error('province') is-invalid @enderror"
                                   placeholder="DKI Jakarta"
                                   value="{{ old('province') }}" required>
                            @error('province')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                        {{-- Kode Pos --}}
                        <div class="col-md-3">
                            <label class="form-label" for="postal_code">Kode Pos <span class="text-danger">*</span></label>
                            <input type="text" name="postal_code" id="postal_code"
                                   class="form-control @error('postal_code') is-invalid @enderror"
                                   placeholder="12345"
                                   value="{{ old('postal_code') }}" required>
                            @error('postal_code')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    {{-- Password --}}
                    <div class="mb-3">
                        <label class="form-label" for="password">Password <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="password"
                                   class="form-control pe-5 @error('password') is-invalid @enderror"
                                   placeholder="Min. 8 karakter">
                            <span class="password-toggle" onclick="togglePwd('password','eyeIcon1')">
                                <i id="eyeIcon1" class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    {{-- Confirm password --}}
                    <div class="mb-4">
                        <label class="form-label" for="password_confirmation">Konfirmasi Password <span class="text-danger">*</span></label>
                        <div class="input-icon-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                   class="form-control pe-5"
                                   placeholder="Ulangi password">
                            <span class="password-toggle" onclick="togglePwd('password_confirmation','eyeIcon2')">
                                <i id="eyeIcon2" class="fa-solid fa-eye"></i>
                            </span>
                        </div>
                    </div>

                    <button type="submit" class="btn-auth">
                        <i class="fa-solid fa-user-plus me-2"></i>Buat Akun Gratis
                    </button>
                </form>

                <div class="auth-footer mt-3">
                    Sudah punya akun? <a href="{{ route('customer.login') }}">Masuk di sini</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePwd(inputId, iconId) {
        const input = document.getElementById(inputId);
        const icon  = document.getElementById(iconId);
        if (input.type === 'password') {
            input.type = 'text';
            icon.classList.replace('fa-eye', 'fa-eye-slash');
        } else {
            input.type = 'password';
            icon.classList.replace('fa-eye-slash', 'fa-eye');
        }
    }
</script>
@endpush
