@extends('layouts.shop')

@section('title', 'Pesanan Saya — MotorParts Store')

@push('styles')
<style>
    .orders-header {
        background: linear-gradient(135deg, var(--dark), var(--dark2));
        padding: 2rem 0;
        color: #fff;
    }
    .orders-header h2 { font-weight: 800; margin-bottom: .25rem; }
    .orders-header p { color: rgba(255,255,255,.5); margin: 0; font-size: .9rem; }

    .order-card {
        background: #fff;
        border: 1px solid #e5e7eb;
        border-radius: 14px;
        transition: all 0.2s;
        overflow: hidden;
    }
    .order-card:hover {
        border-color: var(--primary);
        box-shadow: 0 6px 20px rgba(0,0,0,.06);
    }
    .order-card .order-header {
        padding: 1rem 1.25rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: .5rem;
    }
    .order-card .order-body {
        padding: 0 1.25rem 1.25rem;
    }

    .tier-mini {
        display: flex;
        gap: 3px;
        margin-bottom: .75rem;
    }
    .tier-mini .step {
        flex: 1;
        height: 5px;
        border-radius: 3px;
        background: #e5e7eb;
    }
    .tier-mini .step.done { background: #10B981; }
    .tier-mini .step.current { background: #3B82F6; }

    .order-number { font-weight: 700; font-size: 1.05rem; color: var(--dark); }
    .order-date { font-size: .8rem; color: #9ca3af; }
    .order-total-label { font-size: .78rem; color: #6b7280; }
    .order-total { font-weight: 700; font-size: 1.15rem; color: var(--primary); }

    .badge-status {
        font-size: .8rem;
        padding: .4em .75em;
        border-radius: 6px;
        font-weight: 600;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 1rem;
    }
    .empty-state i { font-size: 3.5rem; color: #d1d5db; margin-bottom: 1rem; }
    .empty-state h5 { font-weight: 700; color: #6b7280; }
    .empty-state p { color: #9ca3af; font-size: .9rem; }

    @media (max-width: 576px) {
        .order-card .order-header { padding: .8rem 1rem; }
        .order-card .order-body { padding: 0 1rem 1rem; }
        .order-number { font-size: .95rem; }
        .order-total { font-size: 1rem; }
    }
</style>
@endpush

@section('content')

{{-- Header --}}
<div class="orders-header">
    <div class="container">
        <h2><i class="fa-solid fa-box-open me-2"></i>Pesanan Saya</h2>
        <p>Riwayat dan status seluruh pesanan Anda</p>
    </div>
</div>

<div class="container py-4">
    @if($orders->isEmpty())
        <div class="empty-state bg-white rounded-4 shadow-sm">
            <i class="fa-solid fa-receipt d-block"></i>
            <h5>Belum Ada Pesanan</h5>
            <p>Anda belum pernah melakukan transaksi di toko kami.</p>
            <a href="{{ route('shop.catalog') }}" class="btn btn-primary-shop px-4 mt-2">
                <i class="fa-solid fa-bag-shopping me-2"></i>Mulai Belanja
            </a>
        </div>
    @else
        <div class="d-flex flex-column gap-3">
            @foreach($orders as $order)
            @php
                $tierKeys = [
                    \App\Models\Order::STATUS_DIBAYAR,
                    \App\Models\Order::STATUS_DIPROSES,
                    \App\Models\Order::STATUS_DIKIRIM,
                    \App\Models\Order::STATUS_SUDAH_TIBA,
                    \App\Models\Order::STATUS_SELESAI,
                ];
                $currentIdx = array_search($order->status, $tierKeys);
            @endphp
            <div class="order-card">
                <div class="order-header">
                    <div>
                        <span class="order-date">
                            <i class="fa-regular fa-calendar me-1"></i>{{ $order->created_at->format('d M Y, H:i') }}
                        </span>
                        <div class="order-number mt-1">{{ $order->order_number }}</div>
                    </div>
                    <span class="badge {{ $order->statusBadgeClass() }} badge-status">
                        {{ $order->statusLabel() }}
                    </span>
                </div>
                <div class="order-body">
                    {{-- 5-Tier Mini Progress --}}
                    @if($currentIdx !== false)
                    <div class="tier-mini">
                        @foreach($tierKeys as $idx => $status)
                            @php
                                $stepClass = '';
                                if ($idx < $currentIdx) $stepClass = 'done';
                                elseif ($idx === $currentIdx) $stepClass = 'current';
                            @endphp
                            <div class="step {{ $stepClass }}" title="{{ ucwords(str_replace('_', ' ', $status)) }}"></div>
                        @endforeach
                    </div>
                    @endif

                    <div class="d-flex justify-content-between align-items-end flex-wrap gap-2">
                        <div>
                            <div class="order-total-label">Total Belanja</div>
                            <div class="order-total">{{ $order->formattedTotal() }}</div>
                            @if($order->tracking_number)
                                <div class="mt-1 small fw-bold" style="color: var(--primary);">
                                    <i class="fa-solid fa-truck-fast me-1"></i> Resi: {{ $order->tracking_number }}
                                </div>
                            @endif
                        </div>
                        <a href="{{ route('shop.orders.show', $order->order_number) }}" class="btn btn-outline-primary btn-sm px-3">
                            <i class="fa-solid fa-eye me-1"></i> Lacak / Detail
                        </a>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        @if($orders->hasPages())
        <div class="mt-4 d-flex justify-content-center">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
        @endif
    @endif
</div>
@endsection
