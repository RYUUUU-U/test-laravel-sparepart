<!DOCTYPE html>
<html>
<head>
    <title>Export Data Ke Excel</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; text-align: left; }
        .judul { border: 0; text-align: center; font-weight: bold; font-size: 18px; background-color: #ffffff; }
        .sub-judul { border: 0; text-align: center; font-size: 14px; background-color: #ffffff; }
        .header-tabel { background-color: #f2f2f2; text-align: center; }
    </style>
</head>
<body>
<table>
    <thead>
        <tr><th colspan="5" class="judul">LAPORAN PENJUALAN BARANG</th></tr>
        <tr><th colspan="5" class="sub-judul">{{ $labelWaktu }}</th></tr>
        <tr><th colspan="5" style="border: 0;"></th></tr>
        <tr>
            <th class="header-tabel">No</th>
            <th class="header-tabel">Waktu Transaksi</th>
            <th class="header-tabel">Nama Barang</th>
            <th class="header-tabel">Jumlah Keluar</th>
            <th class="header-tabel">Total Harga</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $no => $d)
        <tr>
            <td style="text-align: center;">{{ $no + 1 }}</td>
            <td style="text-align: center;">{{ $d->tanggal_keluar }}</td>
            <td>{{ $d->barang->nama_barang ?? '-' }}</td>
            <td style="text-align: center;">{{ $d->jumlah_keluar }}</td>
            <td style="text-align: right;">Rp {{ number_format($d->total_harga) }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold; background-color: #f2f2f2;">TOTAL PENDAPATAN</td>
            <td style="text-align: right; font-weight: bold; background-color: #f2f2f2;">Rp {{ number_format($grandTotal) }}</td>
        </tr>
    </tbody>
</table>
</body>
</html>
