@extends('layouts.shop')
@section('title', 'Checkout — MotorParts Store')

@push('styles')
<style>
    .checkout-steps {
        display: flex; gap: 0; margin-bottom: 2.5rem;
        background: #fff; border-radius: 16px;
        padding: 1.2rem 2rem; box-shadow: 0 2px 12px rgba(0,0,0,.06);
    }
    .step { display: flex; align-items: center; gap: .6rem; flex: 1; }
    .step-num {
        width: 32px; height: 32px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: .85rem; flex-shrink: 0;
    }
    .step.done .step-num  { background: #E63946; color: #fff; }
    .step.active .step-num{ background: #E63946; color: #fff; }
    .step.idle .step-num  { background: #f3f4f6; color: #9ca3af; }
    .step-label { font-size: .85rem; font-weight: 600; color: #374151; }
    .step.idle .step-label{ color: #9ca3af; }
    .step-line { flex: 1; height: 2px; background: #e5e7eb; margin: 0 .5rem; }
    .step-line.done { background: #E63946; }

    .form-card {
        background: #fff; border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,.08); padding: 2rem;
    }
    .form-card h5 { font-weight: 800; margin-bottom: 1.2rem; color: #1a1a2e; }
    .form-control, .form-select {
        border: 1.5px solid #e5e7eb; border-radius: 10px;
        padding: .65rem 1rem; font-size: .9rem;
    }
    .form-control:focus, .form-select:focus {
        border-color: #E63946;
        box-shadow: 0 0 0 3px rgba(230,57,70,.1);
    }
    .form-label { font-weight: 600; font-size: .88rem; color: #374151; margin-bottom: .4rem; }

    .summary-sidebar {
        background: #fff; border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,.08);
        padding: 1.8rem; position: sticky; top: 80px;
    }
    .summary-sidebar h5 { font-weight: 800; margin-bottom: 1.2rem; }
    .order-item-row { display: flex; gap: .8rem; align-items: center; padding: .6rem 0; border-bottom: 1px solid #f3f4f6; }
    .order-item-row:last-child { border-bottom: none; }
    .order-item-img { width: 52px; height: 46px; border-radius: 8px; object-fit: cover; flex-shrink: 0; background: #f3f4f6; }
    .order-item-name { font-size: .88rem; font-weight: 600; color: #1a1a2e; }
    .order-item-sub { font-size: .8rem; color: #6b7280; }

    .pay-btn {
        background: linear-gradient(135deg, #E63946, #c1121f);
        border: none; color: #fff; border-radius: 12px;
        padding: .9rem 2rem; font-weight: 700; font-size: 1rem;
        width: 100%; transition: transform .15s, box-shadow .15s;
        box-shadow: 0 4px 16px rgba(230,57,70,.35);
    }
    .pay-btn:hover { transform: translateY(-2px); box-shadow: 0 8px 24px rgba(230,57,70,.4); color: #fff; }
    .pay-btn:active { transform: translateY(0); }

    .dummy-notice {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 1px solid #fcd34d;
        border-radius: 12px;
        padding: 1rem 1.2rem;
        font-size: .85rem;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container py-5">

    {{-- Steps --}}
    <div class="checkout-steps">
        <div class="step done">
            <div class="step-num"><i class="fa-solid fa-check"></i></div>
            <span class="step-label">Keranjang</span>
        </div>
        <div class="step-line done"></div>
        <div class="step active">
            <div class="step-num">2</div>
            <span class="step-label">Detail Pesanan</span>
        </div>
        <div class="step-line"></div>
        <div class="step idle">
            <div class="step-num">3</div>
            <span class="step-label">Konfirmasi</span>
        </div>
    </div>

    <div class="row g-4 align-items-start">

        {{-- ── Form Data Penerima ──────────────────────────────── --}}
        <div class="col-lg-7">

            {{-- Dummy payment notice --}}
            <div class="dummy-notice">
                <i class="fa-solid fa-flask-vial me-2 text-warning"></i>
                <strong>Mode Demo:</strong> Setelah checkout, Anda akan diarahkan ke halaman invoice Xendit.
                Tersedia juga tombol <em>Simulasi Pembayaran</em> untuk menguji alur tanpa pembayaran nyata.
            </div>

            <form id="checkoutForm" action="{{ route('shop.checkout.store') }}" method="POST">
                @csrf

                <div class="form-card mb-4">
                    <h5><i class="fa-solid fa-user me-2 text-danger"></i>Data Penerima</h5>

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name', $customer['name']) }}"
                                   placeholder="Masukkan nama lengkap penerima">
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email', $customer['email']) }}"
                                   placeholder="email@example.com">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">No. Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror"
                                   value="{{ old('phone') }}"
                                   placeholder="08xx-xxxx-xxxx">
                            @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea name="address" rows="3"
                                      class="form-control @error('address') is-invalid @enderror"
                                      placeholder="Jl. Contoh No. 1, Kecamatan, Kota, Provinsi">{{ old('address') }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label">Catatan Tambahan <span class="text-muted small">(opsional)</span></label>
                            <textarea name="notes" rows="2" class="form-control"
                                      placeholder="Instruksi khusus pengiriman...">{{ old('notes') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="form-card">
                    <h5><i class="fa-solid fa-credit-card me-2 text-danger"></i>Metode Pembayaran</h5>
                    <div class="border rounded-3 p-3 d-flex align-items-center gap-3"
                         style="border-color:#E63946 !important;background:#fff5f5">
                        <i class="fa-solid fa-building-columns fa-2x text-primary"></i>
                        <div>
                            <div class="fw-700">Xendit Payment Gateway</div>
                            <div class="small text-muted">Virtual Account, E-Wallet, QRIS, Kartu Kredit & lainnya.</div>
                        </div>
                        <i class="fa-solid fa-circle-check text-success ms-auto fa-xl"></i>
                    </div>
                </div>

            </form>
        </div>

        {{-- ── Order Summary Sidebar ────────────────────────────── --}}
        <div class="col-lg-5">
            <div class="summary-sidebar">
                <h5>Ringkasan Pesanan</h5>

                @foreach($cart as $item)
                <div class="order-item-row">
                    <img src="https://placehold.co/104x92/e2e8f0/64748b?text={{ urlencode($item['code']) }}"
                         alt="{{ $item['name'] }}" class="order-item-img">
                    <div class="flex-grow-1">
                        <div class="order-item-name">{{ $item['name'] }}</div>
                        <div class="order-item-sub">{{ $item['code'] }} × {{ $item['quantity'] }}</div>
                    </div>
                    <div class="fw-700 text-dark small">Rp {{ number_format($item['subtotal'], 0, ',', '.') }}</div>
                </div>
                @endforeach

                <div class="mt-3 pt-2">
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-600">Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2 small">
                        <span class="text-muted">Ongkos Kirim</span>
                        <span class="text-success fw-600">Gratis</span>
                    </div>

                    {{-- Alamat pengiriman preview (live) --}}
                    <div id="addressPreview" class="mb-2 small" style="display:none">
                        <div class="d-flex align-items-center mb-1">
                            <span class="text-muted"><i class="fa-solid fa-location-dot me-1"></i>Dikirim ke:</span>
                        </div>
                        <div class="p-2 rounded-3" style="background:#f0fdf4;border:1px solid #a7f3d0;font-size:.82rem;color:#065f46">
                            <span id="addressText"></span>
                        </div>
                    </div>

                    <hr class="my-2">
                    <div class="d-flex justify-content-between">
                        <span class="fw-800">Total Bayar</span>
                        <span class="fw-800 text-danger fs-5">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                </div>

                <button type="submit" form="checkoutForm" class="pay-btn mt-4">
                    <i class="fa-solid fa-bolt me-2"></i>Bayar Sekarang
                    <span class="d-block small fw-400 mt-1">Rp {{ number_format($total, 0, ',', '.') }}</span>
                </button>

                <div class="text-center mt-3">
                    <small class="text-muted">
                        <i class="fa-solid fa-shield-halved me-1 text-success"></i>
                        Transaksi aman. Anda akan diarahkan ke halaman pembayaran Xendit.
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Live preview alamat pengiriman di sidebar
    document.addEventListener('DOMContentLoaded', function() {
        const addressInput = document.querySelector('textarea[name="address"]');
        const addressPreview = document.getElementById('addressPreview');
        const addressText = document.getElementById('addressText');

        function updatePreview() {
            const value = addressInput.value.trim();
            if (value) {
                addressPreview.style.display = 'block';
                addressText.textContent = value;
            } else {
                addressPreview.style.display = 'none';
            }
        }

        if (addressInput) {
            addressInput.addEventListener('input', updatePreview);
            updatePreview(); // Run on load in case of old() value
        }
    });
</script>
@endpush
@endsection

