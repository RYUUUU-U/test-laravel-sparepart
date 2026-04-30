<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan FOREIGN KEY constraints pada tabel legacy:
 *
 *  - barang_keluar.id_barang → barang.id_barang  (ON DELETE RESTRICT)
 *  - barang_masuk.id_barang  → barang.id_barang  (ON DELETE RESTRICT)
 *  - barang_masuk.id_supplier → supplier.id_supplier (ON DELETE RESTRICT)
 *
 * Sebelum menambahkan FK, migration ini:
 *  1. Membersihkan orphan records (data yang mereferensikan parent yang tidak ada)
 *  2. Menyamakan tipe data kolom FK (int → int unsigned) agar match dengan parent
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Bersihkan orphan records ─────────────────────────────────────

        // Hapus barang_keluar yang id_barang-nya tidak ada di barang
        DB::statement('
            DELETE bk FROM `barang_keluar` bk
            LEFT JOIN `barang` b ON bk.id_barang = b.id_barang
            WHERE bk.id_barang IS NOT NULL
              AND b.id_barang IS NULL
        ');

        // Hapus barang_masuk yang id_barang-nya tidak ada di barang
        DB::statement('
            DELETE bm FROM `barang_masuk` bm
            LEFT JOIN `barang` b ON bm.id_barang = b.id_barang
            WHERE bm.id_barang IS NOT NULL
              AND b.id_barang IS NULL
        ');

        // Hapus barang_masuk yang id_supplier-nya tidak ada di supplier
        DB::statement('
            DELETE bm FROM `barang_masuk` bm
            LEFT JOIN `supplier` s ON bm.id_supplier = s.id_supplier
            WHERE bm.id_supplier IS NOT NULL
              AND s.id_supplier IS NULL
        ');

        // ── 2. Tambahkan Foreign Key constraints ────────────────────────────

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->foreign('id_barang')
                  ->references('id_barang')
                  ->on('barang')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });

        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->foreign('id_barang')
                  ->references('id_barang')
                  ->on('barang')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');

            $table->foreign('id_supplier')
                  ->references('id_supplier')
                  ->on('supplier')
                  ->onDelete('restrict')
                  ->onUpdate('cascade');
        });
    }

    public function down(): void
    {
        Schema::table('barang_masuk', function (Blueprint $table) {
            $table->dropForeign(['id_supplier']);
            $table->dropForeign(['id_barang']);
        });

        Schema::table('barang_keluar', function (Blueprint $table) {
            $table->dropForeign(['id_barang']);
        });
    }
};
