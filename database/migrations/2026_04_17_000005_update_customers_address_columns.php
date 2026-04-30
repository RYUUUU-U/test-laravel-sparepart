<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Perbaikan tabel customers untuk validasi alamat:
 *
 *  1. Isi data address kosong dengan placeholder
 *  2. Ubah kolom address menjadi NOT NULL
 *  3. Tambah kolom pelengkap: city, province, postal_code
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Isi data address yang kosong/null ─────────────────────────────
        DB::table('customers')
            ->whereNull('address')
            ->orWhere('address', '')
            ->update(['address' => 'Alamat belum diisi']);

        // ── 2. Ubah address menjadi NOT NULL + tambah kolom pelengkap ────────
        Schema::table('customers', function (Blueprint $table) {
            // Ubah address menjadi NOT NULL
            $table->text('address')->nullable(false)->change();

            // Tambah kolom alamat pelengkap
            $table->string('city', 100)->nullable()->after('address');
            $table->string('province', 100)->nullable()->after('city');
            $table->string('postal_code', 10)->nullable()->after('province');
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            // Kembalikan address menjadi nullable
            $table->text('address')->nullable()->change();

            // Hapus kolom pelengkap
            $table->dropColumn(['city', 'province', 'postal_code']);
        });
    }
};
