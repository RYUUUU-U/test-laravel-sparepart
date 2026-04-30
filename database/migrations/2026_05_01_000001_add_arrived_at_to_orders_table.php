<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan kolom arrived_at untuk mendukung alur 5-tier order status.
 *
 * Kolom status sudah menggunakan string, sehingga tidak perlu
 * mengubah tipe kolom — cukup tambahkan kolom timestamp baru.
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            if (!Schema::hasColumn('orders', 'arrived_at')) {
                $table->timestamp('arrived_at')->nullable()->after('shipped_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn('arrived_at');
        });
    }
};
