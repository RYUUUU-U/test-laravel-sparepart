<?php

namespace App\Services;

use App\Models\Barang;

class BarangService
{
    /**
     * Generate kode barang otomatis dari nama barang.
     * Native logic from tambah_barang.php
     *
     * Contoh: "Oli Mesin" → "OLI-001", "OLI-002", dst.
     */
    public function generateKode(string $nama): string
    {
        $prefix = strtoupper(substr($nama, 0, 3));

        $terakhir = Barang::where('kode_barang', 'LIKE', "$prefix-%")
            ->orderByDesc('id_barang')
            ->value('kode_barang');

        if ($terakhir) {
            $pecah  = explode('-', $terakhir);
            $angka  = intval($pecah[1] ?? 0) + 1;
        } else {
            $angka = 1;
        }

        return $prefix . '-' . str_pad($angka, 3, '0', STR_PAD_LEFT);
    }
}
