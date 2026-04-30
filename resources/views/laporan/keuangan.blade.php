@extends('layouts.app')
@section('title', 'Laporan Keuangan')
@section('content')

<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fa-solid fa-coins text-warning me-2"></i>Laporan Keuangan</h1>
    <span class="badge bg-dark px-3 py-2">{{ $labelWaktu }}</span>
</div>

{{-- Filter Tanggal --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.keuangan') }}" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small fw-bold">Tanggal Mulai</label>
                <input type="date" name="tgl_mulai" class="form-control" value="{{ $tglMulai }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small fw-bold">Tanggal Selesai</label>
                <input type="date" name="tgl_selesai" class="form-control" value="{{ $tglSelesai }}">
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-filter me-1"></i> Filter
                </button>
                @if($tglMulai || $tglSelesai)
                    <a href="{{ route('laporan.keuangan') }}" class="btn btn-outline-secondary ms-2">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card bg-success text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Revenue Online</h6>
                        <h3 class="my-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h3>
                        <small>{{ $orderCount }} pesanan</small>
                    </div>
                    <i class="fa-solid fa-globe fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-primary text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Revenue Offline</h6>
                        <h3 class="my-2">Rp {{ number_format($offlineRevenue, 0, ',', '.') }}</h3>
                        <small>{{ $offlineCount }} transaksi</small>
                    </div>
                    <i class="fa-solid fa-store fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-warning text-dark shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Rata-rata Pesanan</h6>
                        <h3 class="my-2">Rp {{ number_format($avgOrder, 0, ',', '.') }}</h3>
                        <small>per pesanan online</small>
                    </div>
                    <i class="fa-solid fa-calculator fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3 mb-3">
        <div class="card bg-danger text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Keseluruhan</h6>
                        <h3 class="my-2">Rp {{ number_format($grandTotal, 0, ',', '.') }}</h3>
                        <small>online + offline</small>
                    </div>
                    <i class="fa-solid fa-sack-dollar fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Pesanan Online --}}
<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">
        <i class="fa-solid fa-list me-2 text-primary"></i> Detail Pesanan Online (Sudah Dibayar)
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">No</th>
                        <th>No. Pesanan</th>
                        <th>Pelanggan</th>
                        <th>Tanggal Bayar</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Total</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($orders as $no => $order)
                    <tr>
                        <td class="ps-3">{{ $no + 1 }}</td>
                        <td>
                            <a href="{{ route('admin.orders.show', $order->id) }}" class="fw-bold text-decoration-none">
                                {{ $order->order_number }}
                            </a>
                        </td>
                        <td>{{ $order->customer_name }}</td>
                        <td>{{ $order->paid_at ? $order->paid_at->format('d/m/Y H:i') : '-' }}</td>
                        <td><span class="badge {{ $order->statusBadgeClass() }}">{{ $order->statusLabel() }}</span></td>
                        <td class="text-end pe-3 fw-bold">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Tidak ada pesanan yang sudah dibayar untuk periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($orders->count())
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end fw-bold pe-3">Total Revenue Online</td>
                        <td class="text-end pe-3 fw-bold text-success fs-5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection
