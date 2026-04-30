@extends('layouts.shop')
@section('title', 'Pesanan Berhasil — MotorParts Store')

@push('styles')
<style>
    .success-wrapper {
        min-height: 70vh; display: flex; align-items: center; justify-content: center;
        padding: 3rem 0;
    }
    .success-card {
        background: #fff; border-radius: 24px;
        box-shadow: 0 8px 40px rgba(0,0,0,.1);
        padding: 3rem 2.5rem; text-align: center; max-width: 720px; width: 100%;
    }
    .success-icon-wrap {
        width: 100px; height: 100px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1.5rem;
        animation: popIn .5s cubic-bezier(.175,.885,.32,1.275);
    }
    .success-icon-wrap.paid {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
    }
    .success-icon-wrap.paid i { font-size: 2.8rem; color: #059669; }

    .success-icon-wrap.unpaid {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
    }
    .success-icon-wrap.unpaid i { font-size: 2.8rem; color: #d97706; }

    @keyframes popIn {
        0%  { transform: scale(0); opacity: 0; }
        100%{ transform: scale(1); opacity: 1; }
    }

    .order-badge {
        display: inline-block;
        background: #f0fdf4; border: 1.5px solid #a7f3d0;
        color: #065f46; border-radius: 40px;
        padding: .4rem 1.2rem; font-weight: 700; font-size: .9rem;
        margin-bottom: 1rem;
    }
    .order-badge.unpaid {
        background: #fffbeb; border-color: #fcd34d; color: #92400e;
    }
    .order-details {
        background: #f8f9fa; border-radius: 16px;
        padding: 1.5rem; text-align: left; margin: 1.5rem 0;
    }
    .order-details h6 { font-weight: 700; margin-bottom: 1rem; color: #374151; }
    .detail-row { display: flex; justify-content: space-between; padding: .5rem 0; font-size: .9rem; border-bottom: 1px solid #e5e7eb; }
    .detail-row:last-child { border-bottom: none; }
    .detail-row span:first-child { color: #6b7280; }
    .detail-row span:last-child { font-weight: 600; color: #1a1a2e; }

    .items-list { text-align: left; margin: 1rem 0; }
    .item-row { display: flex; justify-content: space-between; padding: .4rem 0; font-size: .88rem; border-bottom: 1px dashed #e5e7eb; }
    .item-row:last-child { border-bottom: none; }

    .total-banner {
        background: linear-gradient(135deg, #E63946, #c1121f);
        border-radius: 12px; color: #fff; padding: 1rem 1.5rem;
        display: flex; justify-content: space-between; align-items: center;
        margin: 1.5rem 0;
    }
    .total-banner .label { font-size: .9rem; opacity: .85; }
    .total-banner .amount { font-size: 1.4rem; font-weight: 800; }

    /* ── Simulate Payment Button (Dummy Mode) ─────────────────── */
    .simulate-section {
        background: linear-gradient(135deg, #fffbeb, #fef3c7);
        border: 2px dashed #fbbf24;
        border-radius: 16px;
        padding: 1.5rem;
        margin: 1.5rem 0;
        text-align: center;
    }
    .simulate-section h6 {
        font-weight: 800; color: #92400e; margin-bottom: .5rem;
    }
    .simulate-section p {
        font-size: .85rem; color: #78716c; margin-bottom: 1rem;
    }
    .btn-simulate {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        border: none; color: #fff; border-radius: 12px;
        padding: .8rem 2rem; font-weight: 700; font-size: 1rem;
        box-shadow: 0 4px 16px rgba(245,158,11,.4);
        transition: transform .15s, box-shadow .15s;
    }
    .btn-simulate:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(245,158,11,.5);
        color: #fff;
    }
    .btn-simulate:active { transform: translateY(0); }

    .btn-xendit {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border: none; color: #fff; border-radius: 12px;
        padding: .8rem 2rem; font-weight: 700; font-size: 1rem;
        box-shadow: 0 4px 16px rgba(59,130,246,.4);
        transition: transform .15s, box-shadow .15s;
        text-decoration: none;
    }
    .btn-xendit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(59,130,246,.5);
        color: #fff;
    }

    .action-buttons { display: flex; gap: 1rem; justify-content: center; flex-wrap: wrap; margin-top: 2rem; }

    .flash-alert {
        border-radius: 12px; padding: 1rem 1.5rem;
        font-size: .9rem; margin-bottom: 1.5rem;
        animation: slideDown .3s ease-out;
    }
    @keyframes slideDown {
        0% { transform: translateY(-10px); opacity: 0; }
        100% { transform: translateY(0); opacity: 1; }
    }
</style>
@endpush

@section('content')
<div class="container success-wrapper">
    <div class="success-card">

        {{-- Flash Messages --}}
        @if(session('success'))
        <div class="flash-alert alert alert-success">
            <i class="fa-solid fa-circle-check me-2"></i>{!! session('success') !!}
        </div>
        @endif
        @if(session('error'))
        <div class="flash-alert alert alert-danger">
            <i class="fa-solid fa-circle-xmark me-2"></i>{!! session('error') !!}
        </div>
        @endif
        @if(session('info'))
        <div class="flash-alert alert alert-info">
            <i class="fa-solid fa-circle-info me-2"></i>{!! session('info') !!}
        </div>
        @endif

        {{-- Icon --}}
        <div class="success-icon-wrap {{ $order->isPaid() ? 'paid' : 'unpaid' }}">
            @if($order->isPaid())
                <i class="fa-solid fa-check"></i>
            @else
                <i class="fa-solid fa-clock"></i>
            @endif
        </div>

        {{-- Heading --}}
        <div class="order-badge {{ $order->isPaid() ? '' : 'unpaid' }}">
            <i class="fa-solid fa-receipt me-1"></i>{{ $order->order_number }}
        </div>

        @if($order->isPaid())
            <h2 style="font-size:1.7rem;font-weight:800;color:#1a1a2e;margin-bottom:.4rem">Pesanan Berhasil! 🎉</h2>
            <p class="text-muted">Terima kasih, <strong>{{ $order->customer_name }}</strong>! Pesanan Anda telah diterima dan pembayaran berhasil diproses.</p>
        @elseif($order->status === \App\Models\Order::STATUS_DIBATALKAN)
            <h2 style="font-size:1.7rem;font-weight:800;color:#dc3545;margin-bottom:.4rem">Pesanan Dibatalkan ❌</h2>
            <p class="text-muted">Pesanan ini telah dibatalkan karena melewati batas waktu pembayaran 24 jam atau ditolak.</p>
        @else
            <h2 style="font-size:1.7rem;font-weight:800;color:#1a1a2e;margin-bottom:.4rem">Menunggu Pembayaran ⏳</h2>
            <p class="text-muted mb-1">Pesanan Anda telah dibuat, <strong>{{ $order->customer_name }}</strong>.</p>
            
            <div class="alert alert-warning d-inline-block px-4 py-2 mt-2 border-warning" style="border-radius: 12px; background: #fffbeb;">
                <i class="fa-regular fa-clock me-2 text-warning"></i>Sisa waktu pembayaran:
                <strong id="countdown-timer" class="fs-5 text-danger ms-2">00:00:00</strong>
            </div>
            
            <script>
                document.addEventListener('DOMContentLoaded', function() {
                    const expireTime = new Date("{{ $order->created_at->addHours(24)->format('Y-m-d\TH:i:s') }}").getTime();
                    const timerEl = document.getElementById('countdown-timer');
                    
                    const interval = setInterval(function() {
                        const now = new Date().getTime();
                        const distance = expireTime - now;
                        
                        if (distance < 0) {
                            clearInterval(interval);
                            timerEl.innerHTML = "WAKTU HABIS";
                            location.reload(); // Reload to show cancelled status
                            return;
                        }
                        
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                        
                        timerEl.innerHTML = 
                            (hours < 10 ? "0" + hours : hours) + ":" + 
                            (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                            (seconds < 10 ? "0" + seconds : seconds);
                    }, 1000);
                });
            </script>
        @endif

        {{-- ── SIMULATE PAYMENT SECTION (only for unpaid orders) ──── --}}
        @if(!$order->isPaid() && $order->status === App\Models\Order::STATUS_AWAITING_PAYMENT)
        <div class="simulate-section">
            <h6><i class="fa-solid fa-flask-vial me-2"></i>Mode Demo — Simulasi Pembayaran</h6>
            <p>Karena ini proyek dummy, Anda bisa mensimulasikan pembayaran berhasil tanpa gateway nyata. Stok akan dikurangi otomatis.</p>

            <div class="d-flex gap-3 justify-content-center flex-wrap">
                {{-- Tombol bayar via Xendit (jika URL tersedia) --}}
                @if($order->xendit_invoice_url)
                <a href="{{ $order->xendit_invoice_url }}" target="_blank" class="btn-xendit">
                    <i class="fa-solid fa-arrow-up-right-from-square me-2"></i>Bayar via Xendit
                </a>
                @endif

                {{-- Tombol Simulasi Pembayaran --}}
                <form action="{{ route('shop.order.simulate', encrypt($order->order_number)) }}" method="POST"
                      onsubmit="return confirm('Anda yakin ingin mensimulasikan pembayaran lunas?\n\nStok barang akan dikurangi otomatis.');">
                    @csrf
                    <button type="submit" class="btn-simulate" id="btn-simulate-payment">
                        <i class="fa-solid fa-wand-magic-sparkles me-2"></i>Simulasikan Pembayaran Berhasil (Dummy)
                    </button>
                </form>
            </div>
        </div>
        @endif

        {{-- Order Details --}}
        <div class="order-details">
            <h6><i class="fa-solid fa-info-circle me-2 text-danger"></i>Detail Pesanan</h6>

            <div class="detail-row">
                <span>Nomor Pesanan</span>
                <span class="text-danger">{{ $order->order_number }}</span>
            </div>
            <div class="detail-row">
                <span>Tanggal</span>
                <span>{{ $order->created_at->format('d M Y, H:i') }} WIB</span>
            </div>
            <div class="detail-row">
                <span>Status Pesanan</span>
                <span class="badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span>
            </div>
            <div class="detail-row">
                <span>Status Pembayaran</span>
                <span class="badge {{ $order->paymentBadgeClass() }}">
                    {{ $order->payment_status === 'paid' ? '✓ Lunas' : 'Belum Dibayar' }}
                </span>
            </div>
            <div class="detail-row">
                <span>Metode Bayar</span>
                <span>{{ $order->payment_method }}</span>
            </div>
            <div class="detail-row">
                <span>Penerima</span>
                <span>{{ $order->customer_name }}</span>
            </div>
            <div class="detail-row">
                <span>Telepon</span>
                <span>{{ $order->customer_phone }}</span>
            </div>
            <div class="detail-row">
                <span>Alamat Pengiriman</span>
                <span class="text-end" style="max-width:55%">{{ $order->customer_address }}</span>
            </div>
        </div>

        {{-- Items --}}
        <div class="order-details">
            <h6><i class="fa-solid fa-boxes-stacked me-2 text-danger"></i>Item Dipesan</h6>
            <div class="items-list">
                @foreach($order->items as $item)
                <div class="item-row">
                    <span>{{ $item->product_name }} <span class="text-muted">×{{ $item->quantity }}</span></span>
                    <span class="fw-600">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                </div>
                @endforeach
            </div>
        </div>

        {{-- Total --}}
        <div class="total-banner">
            <div>
                <div class="label">Ongkos Kirim: <strong>Gratis</strong></div>
                <div class="label">Total Pembayaran</div>
            </div>
            <div class="amount">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</div>
        </div>

        {{-- Actions --}}
        <div class="action-buttons">
            <a href="{{ route('shop.invoice', encrypt($order->order_number)) }}" class="btn btn-outline-danger btn-lg px-4">
                <i class="fa-solid fa-file-invoice me-2"></i>Lihat Invoice
            </a>
            <a href="{{ route('shop.home') }}" class="btn btn-outline-shop btn-lg px-4">
                <i class="fa-solid fa-home me-2"></i>Kembali ke Beranda
            </a>
            <a href="{{ route('shop.catalog') }}" class="btn btn-primary-shop btn-lg px-4">
                <i class="fa-solid fa-th-large me-2"></i>Belanja Lagi
            </a>
        </div>

        <p class="text-muted small mt-4 mb-0">
            <i class="fa-solid fa-envelope me-1"></i>
            Konfirmasi pesanan dikirim ke <strong>{{ $order->customer_email }}</strong>
        </p>
    </div>
</div>
@endsection
