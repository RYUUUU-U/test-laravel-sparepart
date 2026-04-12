<!DOCTYPE html>
<html>
<head>
    <title>Export Barang Masuk</title>
    <style>
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; }
        th, td { padding: 8px; }
        .judul { border: 0; text-align: center; font-weight: bold; font-size: 18px; background-color: #ffffff; }
        .sub-judul { border: 0; text-align: center; font-size: 14px; background-color: #ffffff; }
        .header-tabel { background-color: #f2f2f2; text-align: center; }
    </style>
</head>
<body>
<table>
    <thead>
        <tr><th colspan="5" class="judul">LAPORAN BARANG MASUK (RESTOCK)</th></tr>
        <tr><th colspan="5" class="sub-judul">{{ $labelWaktu }}</th></tr>
        <tr><th colspan="5" style="border: 0;"></th></tr>
        <tr>
            <th class="header-tabel">No</th>
            <th class="header-tabel">Tanggal Masuk</th>
            <th class="header-tabel">Nama Barang</th>
            <th class="header-tabel">Supplier</th>
            <th class="header-tabel">Jumlah Masuk</th>
        </tr>
    </thead>
    <tbody>
        @foreach($data as $no => $d)
        <tr>
            <td style="text-align: center;">{{ $no + 1 }}</td>
            <td style="text-align: center;">{{ $d->tanggal_masuk }}</td>
            <td>{{ $d->barang->nama_barang ?? '-' }}</td>
            <td>{{ $d->supplier->nama_supplier ?? '-' }}</td>
            <td style="text-align: center;">+ {{ $d->jumlah_masuk }}</td>
        </tr>
        @endforeach
        <tr>
            <td colspan="4" style="text-align: right; font-weight: bold; background-color: #f2f2f2;">TOTAL BARANG MASUK</td>
            <td style="text-align: center; font-weight: bold; background-color: #f2f2f2;">{{ $totalMasuk }}</td>
        </tr>
    </tbody>
</table>
</body>
</html>
