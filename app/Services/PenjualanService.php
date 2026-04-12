<?php

namespace App\Services;

use App\Models\Barang;
use App\Models\BarangKeluar;

class PenjualanService
{
    /**
     * Proses dan simpan transaksi penjualan (barang keluar).
     * Native: form_penjualan.php
     *
     * @return array ['success' => bool, 'message' => string]
     */
    public function simpanTransaksi(int $idBarang, int $jumlah, string $tanggal): array
    {
        $barang = Barang::find($idBarang);

        if (!$barang) {
            return ['success' => false, 'message' => 'Barang tidak ditemukan.'];
        }

        if ($barang->stok < $jumlah) {
            return ['success' => false, 'message' => "Stok tidak mencukupi! Sisa stok: {$barang->stok} unit."];
        }

        $totalBayar = $jumlah * $barang->harga_jual;

        BarangKeluar::create([
            'id_barang'      => $idBarang,
            'tanggal_keluar' => $tanggal,
            'jumlah_keluar'  => $jumlah,
            'total_harga'    => $totalBayar,
        ]);

        // Kurangi stok
        $barang->decrement('stok', $jumlah);

        return ['success' => true, 'message' => 'Transaksi berhasil disimpan!'];
    }
}
