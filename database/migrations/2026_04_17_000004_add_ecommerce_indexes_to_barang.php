<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan index pada tabel barang untuk performa query e-commerce:
 *
 *  - idx_barang_kategori       : Filter produk berdasarkan kategori
 *  - idx_barang_is_active      : Filter produk aktif/nonaktif
 *  - idx_barang_active_stok    : Composite index untuk query "produk aktif dengan stok > 0"
 *  - ft_barang_search          : FULLTEXT index untuk pencarian teks nama & deskripsi
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            // Index tunggal
            $table->index('kategori', 'idx_barang_kategori');
            $table->index('is_active', 'idx_barang_is_active');

            // Composite index: query paling umum di catalog (WHERE is_active = 1 AND stok > 0)
            $table->index(['is_active', 'stok'], 'idx_barang_active_stok');
        });

        // FULLTEXT index — gunakan raw statement karena Blueprint tidak support fulltext di semua driver
        DB::statement('
            ALTER TABLE `barang`
            ADD FULLTEXT INDEX `ft_barang_search` (`nama_barang`, `deskripsi`)
        ');
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropIndex('idx_barang_kategori');
            $table->dropIndex('idx_barang_is_active');
            $table->dropIndex('idx_barang_active_stok');
            $table->dropIndex('ft_barang_search');
        });
    }
};
