<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'awaiting_payment', 'dibayar', 'diproses', 'dikirim', 'sudah_tiba', 'selesai', 'pesanan_disiapkan', 'disetujui', 'sedang_dikirim', 'barang_akan_dikirim', 'dibatalkan', 'gagal') DEFAULT 'pending'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \Illuminate\Support\Facades\DB::statement("ALTER TABLE orders MODIFY COLUMN status ENUM('pending', 'awaiting_payment', 'pesanan_disiapkan', 'disetujui', 'barang_akan_dikirim', 'selesai', 'dibatalkan', 'gagal') DEFAULT 'pending'");
    }
};
