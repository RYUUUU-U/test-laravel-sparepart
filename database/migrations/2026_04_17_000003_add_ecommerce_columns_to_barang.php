<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Tambahkan kolom e-commerce pada tabel barang:
 *
 *  - deskripsi    : Deskripsi produk lengkap untuk halaman detail
 *  - image_url    : URL gambar utama (path relatif di storage)
 *  - is_active    : Toggle tampil/sembunyi di storefront
 *  - min_order    : Minimum quantity per order
 *  - berat_gram   : Berat produk untuk kalkulasi ongkir
 *  - views        : Counter visit halaman detail produk
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->text('deskripsi')->nullable()->after('satuan');
            $table->string('image_url', 255)->nullable()->after('deskripsi');
            $table->boolean('is_active')->default(true)->after('image_url');
            $table->unsignedInteger('min_order')->default(1)->after('is_active');
            $table->decimal('berat_gram', 10, 2)->default(0)->after('min_order');
            $table->unsignedInteger('views')->default(0)->after('berat_gram');
        });
    }

    public function down(): void
    {
        Schema::table('barang', function (Blueprint $table) {
            $table->dropColumn([
                'deskripsi',
                'image_url',
                'is_active',
                'min_order',
                'berat_gram',
                'views',
            ]);
        });
    }
};
