@extends('layouts.shop')

@section('title', 'Pesanan Saya — MotorParts Store')

@push('styles')
<style>
    .order-card {
        border: 1px solid #e5e7eb;
        border-radius: 12px;
        transition: transform 0.2s;
    }
    .order-card:hover {
        border-color: #E63946;
        box-shadow: 0 4px 12px rgba(0,0,0,0.05);
    }
    .badge-status {
        font-size: 0.85rem;
        padding: 0.5em 0.8em;
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-md-3 mb-4">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-body">
                    <h5 class="card-title fw-bold">Menu Pelanggan</h5>
                    <hr>
                    <ul class="nav flex-column">
                        <li class="nav-item mb-2">
                            <a class="nav-link active fw-bold text-primary px-0" href="{{ route('shop.orders.index') }}">
                                <i class="fa-solid fa-box-open me-2"></i> Pesanan Saya
                            </a>
                        </li>
                        <li class="nav-item">
                            <form action="{{ route('customer.logout') }}" method="POST">
                                @csrf
                                <button type="submit" class="nav-link text-danger border-0 bg-transparent px-0 text-start w-100">
                                    <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <h3 class="fw-bold mb-4">Riwayat Pesanan</h3>

            @if($orders->isEmpty())
                <div class="text-center py-5 bg-white shadow-sm rounded-3">
                    <i class="fa-solid fa-receipt fa-3x text-muted mb-3"></i>
                    <h5 class="fw-bold text-muted">Belum Ada Pesanan</h5>
                    <p class="text-muted mb-4">Anda belum pernah melakukan transaksi di toko kami.</p>
                    <a href="{{ route('shop.catalog') }}" class="btn btn-primary-shop px-4">Mulai Belanja</a>
                </div>
            @else
                @foreach($orders as $order)
                <div class="card order-card mb-3 shadow-sm">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-muted small"><i class="fa-regular fa-calendar me-1"></i> {{ $order->created_at->format('d M Y, H:i') }}</span>
                            <div class="fw-bold fs-5 mt-1">{{ $order->order_number }}</div>
                        </div>
                        <span class="badge {{ $order->statusBadgeClass() }} badge-status">
                            {{ $order->statusLabel() }}
                        </span>
                    </div>
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-md-8">
                                <p class="mb-1 text-muted small">Total Belanja</p>
                                <p class="fw-bold text-danger fs-5 mb-0">{{ $order->formattedTotal() }}</p>
                                
                                @if($order->tracking_number)
                                    <div class="mt-2 text-primary small fw-bold">
                                        <i class="fa-solid fa-truck-fast me-1"></i> Resi: {{ $order->tracking_number }}
                                    </div>
                                @endif
                            </div>
                            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                                <a href="{{ route('shop.orders.show', $order->order_number) }}" class="btn btn-outline-primary w-100">
                                    Lacak / Detail
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="mt-4">
                    {{ $orders->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
