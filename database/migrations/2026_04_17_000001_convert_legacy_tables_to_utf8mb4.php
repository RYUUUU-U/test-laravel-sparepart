<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Konversi charset tabel legacy dari latin1 → utf8mb4.
 *
 * Tabel yang di-convert: barang, barang_keluar, barang_masuk, supplier, users.
 * Diperlukan agar tidak error saat JOIN dengan tabel baru (customers, orders, dll)
 * yang sudah menggunakan utf8mb4.
 */
return new class extends Migration
{
    /**
     * Daftar tabel legacy yang masih latin1.
     */
    private array $tables = [
        'barang',
        'barang_keluar',
        'barang_masuk',
        'supplier',
        'users',
    ];

    public function up(): void
    {
        foreach ($this->tables as $table) {
            // Konversi charset & collation tabel + semua kolom VARCHAR/TEXT sekaligus
            DB::statement(
                "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"
            );
        }
    }

    public function down(): void
    {
        foreach ($this->tables as $table) {
            DB::statement(
                "ALTER TABLE `{$table}` CONVERT TO CHARACTER SET latin1 COLLATE latin1_swedish_ci"
            );
        }
    }
};
