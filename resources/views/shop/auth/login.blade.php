@extends('layouts.shop')
@section('title', 'Login Pelanggan — MotorParts Store')

@push('styles')
<style>
    .auth-wrapper { min-height: 80vh; display:flex; align-items:center; background: linear-gradient(135deg,#f8f9fa 0%,#fff 100%); padding: 3rem 0; }
    .auth-card {
        background:#fff; border-radius:24px;
        box-shadow: 0 8px 40px rgba(0,0,0,.1);
        overflow: hidden; max-width: 460px; width:100%; margin: 0 auto;
    }
    .auth-header {
        background: linear-gradient(135deg, #1a1a2e, #16213e);
        padding: 2.5rem 2.5rem 2rem; text-align:center; color:#fff;
    }
    .auth-header .brand { font-size:1.4rem; font-weight:800; margin-bottom:.3rem; }
    .auth-header .brand span { color:#E63946; }
    .auth-header h2 { font-size:1.3rem; font-weight:700; margin-bottom:.3rem; }
    .auth-header p { color:rgba(255,255,255,.6); font-size:.88rem; margin:0; }

    .auth-body { padding: 2rem 2.5rem; }
    .form-control, .form-select {
        border: 1.5px solid #e5e7eb; border-radius:10px;
        padding: .7rem 1rem; font-size:.9rem;
    }
    .form-control:focus { border-color:#E63946; box-shadow:0 0 0 3px rgba(230,57,70,.1); }
    .form-label { font-weight:600; font-size:.88rem; color:#374151; }
    .input-icon-wrap { position:relative; }
    .input-icon-wrap i { position:absolute; left:1rem; top:50%; transform:translateY(-50%); color:#9ca3af; }
    .input-icon-wrap .form-control { padding-left:2.6rem; }
    .btn-auth {
        background: linear-gradient(135deg,#E63946,#c1121f);
        border:none; color:#fff; border-radius:12px;
        padding:.8rem; font-weight:700; font-size:1rem; width:100%;
        transition: transform .15s, box-shadow .15s;
        box-shadow: 0 4px 16px rgba(230,57,70,.3);
    }
    .btn-auth:hover { transform:translateY(-2px); box-shadow:0 8px 24px rgba(230,57,70,.4); color:#fff; }
    .divider { display:flex; align-items:center; gap:1rem; color:#9ca3af; font-size:.82rem; margin:1.2rem 0; }
    .divider::before, .divider::after { content:''; flex:1; height:1px; background:#e5e7eb; }
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
                <h2>Selamat Datang Kembali</h2>
                <p>Masuk ke akun Anda untuk melanjutkan belanja</p>
            </div>

            {{-- Body --}}
            <div class="auth-body">

                @if($errors->any())
                <div class="alert alert-danger py-2 px-3 mb-3" style="border-radius:10px;font-size:.88rem">
                    <i class="fa-solid fa-triangle-exclamation me-2"></i>{{ $errors->first() }}
                </div>
                @endif

                <form action="{{ route('customer.login.process') }}" method="POST">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Alamat Email</label>
                        <div class="input-icon-wrap">
                            <i class="fa-solid fa-envelope"></i>
                            <input type="email" name="email" id="email"
                                   class="form-control @error('email') is-invalid @enderror"
                                   placeholder="email@example.com"
                                   value="{{ old('email') }}" autofocus>
                        </div>
                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-1">
                        <label class="form-label">Password</label>
                        <div class="input-icon-wrap">
                            <i class="fa-solid fa-lock"></i>
                            <input type="password" name="password" id="password"
                                   class="form-control @error('password') is-invalid @enderror"
                                   placeholder="••••••••">
                        </div>
                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="d-flex justify-content-end mb-3">
                        <a href="#" class="small text-muted text-decoration-none">Lupa password?</a>
                    </div>

                    <button type="submit" class="btn-auth">
                        <i class="fa-solid fa-right-to-bracket me-2"></i>Masuk
                    </button>
                </form>

                <div class="divider">atau</div>

                <a href="{{ route('shop.catalog') }}" class="btn btn-outline-secondary w-100 rounded-3 py-2">
                    <i class="fa-solid fa-store me-2"></i>Lanjut Belanja sebagai Tamu
                </a>

                <div class="auth-footer">
                    Belum punya akun?
                    <a href="{{ route('customer.register') }}">Daftar Sekarang</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
