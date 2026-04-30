@extends('layouts.app')
@section('title', 'Laporan Penjualan')
@section('content')

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="d-flex justify-content-between flex-wrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="fa-solid fa-chart-bar text-primary me-2"></i>Laporan Penjualan</h1>
    <span class="badge bg-dark px-3 py-2">{{ $labelWaktu }}</span>
</div>

{{-- Filter Tanggal --}}
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <form method="GET" action="{{ route('laporan.penjualan') }}" class="row g-3 align-items-end">
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
                    <a href="{{ route('laporan.penjualan') }}" class="btn btn-outline-secondary ms-2">Reset</a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row mb-4">
    <div class="col-md-4 mb-3">
        <div class="card bg-primary text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Item Terjual</h6>
                        <h2 class="my-2">{{ number_format($totalItemsSold) }}</h2>
                        <small>unit/pcs</small>
                    </div>
                    <i class="fa-solid fa-boxes-stacked fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card bg-success text-white shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Revenue</h6>
                        <h2 class="my-2">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</h2>
                        <small>online + offline</small>
                    </div>
                    <i class="fa-solid fa-coins fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card bg-warning text-dark shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Jenis Produk</h6>
                        <h2 class="my-2">{{ $salesData->count() }}</h2>
                        <small>produk terjual</small>
                    </div>
                    <i class="fa-solid fa-tags fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Chart Top 10 --}}
@if($salesData->count() > 0)
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm">
            <div class="card-header bg-white fw-bold">
                <i class="fa-solid fa-chart-simple text-primary me-2"></i> Top 10 Produk Terlaris
            </div>
            <div class="card-body">
                <div style="position: relative; height: 350px; width: 100%;">
                    <canvas id="chartTopSales"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

{{-- Tabel Detail --}}
<div class="card shadow-sm">
    <div class="card-header bg-white fw-bold">
        <i class="fa-solid fa-table me-2 text-primary"></i> Detail Penjualan per Produk
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th class="ps-3">No</th>
                        <th>Kode</th>
                        <th>Nama Produk</th>
                        <th class="text-center">Qty Online</th>
                        <th class="text-center">Qty Offline</th>
                        <th class="text-center">Total Qty</th>
                        <th class="text-end">Revenue Online</th>
                        <th class="text-end">Revenue Offline</th>
                        <th class="text-end pe-3">Total Revenue</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($salesData as $no => $item)
                    <tr>
                        <td class="ps-3">{{ $no + 1 }}</td>
                        <td><span class="badge bg-secondary">{{ $item->product_code }}</span></td>
                        <td class="fw-bold">{{ $item->product_name }}</td>
                        <td class="text-center">{{ number_format($item->qty_online) }}</td>
                        <td class="text-center">{{ number_format($item->qty_offline) }}</td>
                        <td class="text-center fw-bold text-primary">{{ number_format($item->qty_total) }}</td>
                        <td class="text-end">Rp {{ number_format($item->revenue_online, 0, ',', '.') }}</td>
                        <td class="text-end">Rp {{ number_format($item->revenue_offline, 0, ',', '.') }}</td>
                        <td class="text-end pe-3 fw-bold">Rp {{ number_format($item->revenue_total, 0, ',', '.') }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Tidak ada data penjualan untuk periode ini.</td>
                    </tr>
                    @endforelse
                </tbody>
                @if($salesData->count())
                <tfoot class="table-light">
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Grand Total</td>
                        <td class="text-center fw-bold text-primary">{{ number_format($totalItemsSold) }}</td>
                        <td></td>
                        <td></td>
                        <td class="text-end pe-3 fw-bold text-success fs-5">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
@if($salesData->count() > 0)
<script>
    const topData = @json($salesData->take(10));
    const labels = topData.map(d => d.product_name.length > 20 ? d.product_name.substring(0, 20) + '...' : d.product_name);
    
    new Chart(document.getElementById('chartTopSales').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Online',
                    data: topData.map(d => d.qty_online),
                    backgroundColor: '#3B82F6',
                    borderWidth: 0,
                    barPercentage: 0.7,
                },
                {
                    label: 'Offline',
                    data: topData.map(d => d.qty_offline),
                    backgroundColor: '#10B981',
                    borderWidth: 0,
                    barPercentage: 0.7,
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'top', align: 'end', labels: { boxWidth: 12, font: { size: 12 } } },
                tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + ctx.raw + ' unit' } }
            },
            scales: {
                y: { beginAtZero: true, ticks: { stepSize: 1 } },
                x: { grid: { display: false }, ticks: { font: { size: 11 } } }
            }
        }
    });
</script>
@endif
@endpush
