<!DOCTYPE html>
<html>
<head>
    <title>Cetak Laporan Barang Masuk</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none; } }
        body { font-family: Arial, sans-serif; }
        .tanda-tangan { margin-top: 50px; text-align: right; margin-right: 50px; }
    </style>
</head>
<body onload="window.print()">

<div class="container mt-4">
    <div class="text-center mb-4">
        <h3>BENGKEL MOTOR MAJU JAYA</h3>
        <p>Jl. Contoh Alamat Bengkel No. 123, Kota Besar</p>
        <hr>
        <h4>LAPORAN BARANG MASUK (RESTOCK)</h4>
        <span class="text-muted">{{ $label }}</span>
    </div>

    <table class="table table-bordered table-striped">
        <thead class="table-dark">
            <tr>
                <th width="5%">No</th>
                <th>Tanggal Masuk</th>
                <th>Nama Barang</th>
                <th>Supplier</th>
                <th class="text-center">Jumlah</th>
            </tr>
        </thead>
        <tbody>
            @foreach($data as $no => $d)
            <tr>
                <td>{{ $no + 1 }}</td>
                <td>{{ \Carbon\Carbon::parse($d->tanggal_masuk)->format('d/m/Y') }}</td>
                <td>{{ $d->barang->nama_barang ?? '-' }}</td>
                <td>{{ $d->supplier->nama_supplier ?? '-' }}</td>
                <td class="text-center fw-bold text-success">+ {{ $d->jumlah_masuk }}</td>
            </tr>
            @endforeach
            <tr>
                <td colspan="4" class="text-end fw-bold">TOTAL BARANG MASUK</td>
                <td class="text-center fw-bold bg-light">{{ $totalMasuk }}</td>
            </tr>
        </tbody>
    </table>

    <div class="tanda-tangan">
        <p>Dicetak pada: {{ now()->format('d F Y') }}</p>
        <br><br><br>
        <p><u>( Petugas Admin / Kasir )</u></p>
    </div>
</div>

</body>
</html>
