@extends('layouts.app')

@section('title', 'Dashboard Admin')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard Admin</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <button type="button" class="btn btn-sm btn-outline-secondary">
            Tanggal: {{ now()->format('d-m-Y') }}
        </button>
    </div>
</div>

{{-- Kartu Ringkasan --}}
<div class="row">
    <div class="col-md-3 mb-4">
        <div class="card text-white bg-primary shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Barang</h6>
                        <h2 class="my-2">{{ $jmlBarang }}</h2>
                    </div>
                    <i class="fa-solid fa-box fa-3x opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link text-decoration-none" href="{{ route('barang.index') }}">Lihat Detail</a>
                <i class="fa-solid fa-angle-right"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white bg-success shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total Supplier</h6>
                        <h2 class="my-2">{{ $jmlSupplier }}</h2>
                    </div>
                    <i class="fa-solid fa-truck fa-3x opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link text-decoration-none" href="{{ route('supplier.index') }}">Lihat Detail</a>
                <i class="fa-solid fa-angle-right"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white bg-warning shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Transaksi Hari Ini</h6>
                        <h2 class="my-2">{{ $jmlTransaksi }}</h2>
                    </div>
                    <i class="fa-solid fa-cart-shopping fa-3x opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link text-decoration-none" href="{{ route('barang-keluar.index') }}">Lihat Transaksi</a>
                <i class="fa-solid fa-angle-right"></i>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="card text-white bg-danger shadow h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title mb-0">Total User</h6>
                        <h2 class="my-2">{{ $jmlUser }}</h2>
                    </div>
                    <i class="fa-solid fa-users fa-3x opacity-50"></i>
                </div>
            </div>
            <div class="card-footer d-flex align-items-center justify-content-between small">
                <a class="text-white stretched-link text-decoration-none" href="{{ route('user.index') }}">Lihat User</a>
                <i class="fa-solid fa-angle-right"></i>
            </div>
        </div>
    </div>
</div>

{{-- Grafik --}}
<div class="row mt-2" style="align-items: stretch;">
    <div class="col-md-6 mb-4 d-flex">
        <div class="card shadow w-100">
            <div class="card-header bg-white fw-bold">
                <i class="fa-solid fa-chart-simple text-primary"></i> 10 Barang Terlaris (Penjualan)
            </div>
            <div class="card-body">
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="chartTerlaris"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-6 mb-4 d-flex">
        <div class="card shadow w-100">
            <div class="card-header bg-white fw-bold">
                <i class="fa-solid fa-chart-bar text-success"></i> Stok Barang Terbanyak
            </div>
            <div class="card-body">
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="chartStok"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Tabel Stok Menipis --}}
<div class="row mt-2">
    <div class="col-md-12">
        <div class="card shadow">
            <div class="card-header bg-danger text-white">
                <i class="fa-solid fa-triangle-exclamation me-2"></i> Peringatan: Stok Barang Menipis (Kurang dari 5)
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Nama Barang</th>
                                <th>Sisa Stok</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($stokMinim as $no => $d)
                            <tr>
                                <td>{{ $no + 1 }}</td>
                                <td>{{ $d->nama_barang }}</td>
                                <td class="text-danger fw-bold">{{ $d->stok }} Unit</td>
                                <td>
                                    <a href="{{ route('barang-masuk.create') }}" class="btn btn-primary btn-sm">Restock Sekarang</a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center text-success fw-bold">Aman! Tidak ada barang yang stoknya menipis.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const labelTerlaris = @json($terlaris->pluck('nama_barang'));
    const dataTerlaris  = @json($terlaris->pluck('total'));
    const labelStok     = @json($stokTerbanyak->pluck('nama_barang'));
    const dataStok      = @json($stokTerbanyak->pluck('stok'));

    // Chart Terlaris
    new Chart(document.getElementById('chartTerlaris').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labelTerlaris,
            datasets: [{ label: 'Jumlah Terjual', data: dataTerlaris, backgroundColor: '#FF4500', hoverBackgroundColor: '#E03E00', borderWidth: 0, barPercentage: 0.7 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { display: false }, tooltip: { callbacks: { label: ctx => ctx.raw + ' Unit' } } },
            scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } }, x: { grid: { display: false } } }
        }
    });

    // Chart Stok
    new Chart(document.getElementById('chartStok').getContext('2d'), {
        type: 'bar',
        data: {
            labels: labelStok,
            datasets: [{ label: 'Jumlah Stok Saat Ini', data: dataStok, backgroundColor: '#87CEFA', borderColor: '#007bff', borderWidth: 1, barPercentage: 0.6 }]
        },
        options: {
            responsive: true, maintainAspectRatio: false,
            plugins: { legend: { position: 'top', align: 'end', labels: { boxWidth: 12 } } },
            scales: { y: { beginAtZero: true }, x: { grid: { display: false } } }
        }
    });
</script>
@endpush
