@extends('layouts.app')

@section('title', 'Manajemen Pesanan Online')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3"><i class="fa-solid fa-cart-shopping me-2"></i>Pesanan Online</h1>
    <a href="{{ route('dashboard.admin') }}" class="btn btn-outline-secondary">
        <i class="fa-solid fa-arrow-left me-1"></i> Kembali
    </a>
</div>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('admin.orders.index') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Filter Status</label>
                <select name="status" class="form-select" onchange="this.form.submit()">
                    <option value="">Semua Status</option>
                    <option value="awaiting_payment" {{ request('status') == 'awaiting_payment' ? 'selected' : '' }}>Menunggu Pembayaran</option>
                    <option value="dibayar" {{ request('status') == 'dibayar' ? 'selected' : '' }}>Dibayar</option>
                    <option value="diproses" {{ request('status') == 'diproses' ? 'selected' : '' }}>Diproses</option>
                    <option value="dikirim" {{ request('status') == 'dikirim' ? 'selected' : '' }}>Dikirim</option>
                    <option value="sudah_tiba" {{ request('status') == 'sudah_tiba' ? 'selected' : '' }}>Sudah Tiba</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </div>
            <div class="col-md-9 text-end">
                @if(request('status'))
                    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">Reset Filter</a>
                @endif
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0 align-middle">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">No. Pesanan</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Total Bayar</th>
                        <th>Status</th>
                        <th>Pembayaran</th>
                        <th class="text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $order)
                        <tr>
                            <td class="ps-3 fw-bold">{{ $order->order_number }}</td>
                            <td>{{ $order->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                {{ $order->customer_name }}
                                <div class="small text-muted">{{ $order->customer_phone }}</div>
                            </td>
                            <td class="fw-bold text-danger">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                            <td>
                                <span class="badge {{ $order->statusBadgeClass() }}">
                                    {{ $order->statusLabel() }}
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $order->paymentBadgeClass() }}">
                                    {{ strtoupper($order->payment_status) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-info text-white">
                                    <i class="fa-solid fa-eye me-1"></i> Detail
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">Belum ada pesanan online.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @if($orders->hasPages())
        <div class="card-footer bg-white border-0 pt-4 pb-3">
            {{ $orders->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>
@endsection
