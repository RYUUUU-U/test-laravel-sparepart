@extends('layouts.app')

@section('title', 'Dashboard Owner')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Dashboard Owner</h1>
</div>

<div class="alert alert-success">
    Selamat Datang, <b>Owner</b>. Berikut adalah ringkasan performa toko Anda.
</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card text-white bg-primary shadow h-100">
            <div class="card-body">
                <h6 class="card-title">Total Produk Terdaftar</h6>
                <h2 class="my-2">{{ $jmlBarang }} Item</h2>
            </div>
        </div>
    </div>
    <div class="col-md-6 mb-4">
        <div class="card text-white bg-success shadow h-100">
            <div class="card-body">
                <h6 class="card-title">Total Transaksi Penjualan</h6>
                <h2 class="my-2">{{ $jmlTransaksi }} Transaksi</h2>
            </div>
        </div>
    </div>
</div>

<div class="card shadow mb-4">
    <div class="card-header font-weight-bold">Laporan Barang Terlaris</div>
    <div class="card-body">
        <div style="height: 300px;">
            <canvas id="chartOwner"></canvas>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    new Chart(document.getElementById('chartOwner').getContext('2d'), {
        type: 'bar',
        data: {
            labels: @json($terlaris->pluck('nama_barang')),
            datasets: [{ label: 'Jumlah Terjual', data: @json($terlaris->pluck('total')), backgroundColor: '#FF4500' }]
        },
        options: { responsive: true, maintainAspectRatio: false }
    });
</script>
@endpush
