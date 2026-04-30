<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migrasi tabel orders dari Midtrans ke Xendit:
 *
 *  1. Hapus kolom midtrans_*
 *  2. Tambah kolom xendit_*
 *  3. Tambah kolom alur baru (handover, approval, shipping)
 *  4. Update enum status untuk alur pesanan e-commerce baru
 */
return new class extends Migration
{
    public function up(): void
    {
        // ── 1. Hapus kolom Midtrans ─────────────────────────────────────────
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'midtrans_order_id',
                'midtrans_snap_token',
                'midtrans_payment_type',
                'midtrans_transaction_id',
            ]);
        });

        // ── 2. Tambah kolom Xendit ──────────────────────────────────────────
        Schema::table('orders', function (Blueprint $table) {
            $table->string('xendit_external_id', 100)->nullable()->after('payment_method');
            $table->string('xendit_invoice_id', 100)->nullable()->after('xendit_external_id');
            $table->string('xendit_invoice_url', 500)->nullable()->after('xendit_invoice_id');
            $table->string('xendit_payment_channel', 50)->nullable()->after('xendit_invoice_url');
            $table->string('xendit_payment_method', 50)->nullable()->after('xendit_payment_channel');
        });

        // ── 3. Tambah kolom alur baru ───────────────────────────────────────
        Schema::table('orders', function (Blueprint $table) {
            $table->string('handover_photo_url', 500)->nullable()->after('notes');
            $table->timestamp('approved_at')->nullable()->after('handover_photo_url');
            $table->timestamp('shipped_at')->nullable()->after('approved_at');
            $table->unsignedInteger('approved_by')->nullable()->after('shipped_at');
        });

        // ── 4. Update enum status ───────────────────────────────────────────
        // Ubah semua data status lama ke yang cocok di enum baru
        DB::table('orders')->where('status', 'processing')->update(['status' => 'pesanan_disiapkan']);
        DB::table('orders')->where('status', 'completed')->update(['status' => 'selesai']);
        DB::table('orders')->where('status', 'cancelled')->update(['status' => 'dibatalkan']);

        DB::statement("
            ALTER TABLE `orders`
            MODIFY COLUMN `status` ENUM(
                'pending',
                'awaiting_payment',
                'pesanan_disiapkan',
                'disetujui',
                'barang_akan_dikirim',
                'selesai',
                'dibatalkan',
                'gagal'
            ) NOT NULL DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Rollback enum status
        DB::table('orders')->where('status', 'pesanan_disiapkan')->update(['status' => 'processing']);
        DB::table('orders')->where('status', 'disetujui')->update(['status' => 'processing']);
        DB::table('orders')->where('status', 'barang_akan_dikirim')->update(['status' => 'processing']);
        DB::table('orders')->where('status', 'selesai')->update(['status' => 'completed']);
        DB::table('orders')->where('status', 'dibatalkan')->update(['status' => 'cancelled']);
        DB::table('orders')->where('status', 'gagal')->update(['status' => 'cancelled']);
        DB::table('orders')->where('status', 'awaiting_payment')->update(['status' => 'pending']);

        DB::statement("
            ALTER TABLE `orders`
            MODIFY COLUMN `status` ENUM(
                'pending', 'processing', 'completed', 'cancelled'
            ) NOT NULL DEFAULT 'pending'
        ");

        // Hapus kolom alur baru
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn(['handover_photo_url', 'approved_at', 'shipped_at', 'approved_by']);
        });

        // Hapus kolom Xendit
        Schema::table('orders', function (Blueprint $table) {
            $table->dropColumn([
                'xendit_external_id',
                'xendit_invoice_id',
                'xendit_invoice_url',
                'xendit_payment_channel',
                'xendit_payment_method',
            ]);
        });

        // Kembalikan kolom Midtrans
        Schema::table('orders', function (Blueprint $table) {
            $table->string('midtrans_order_id', 100)->nullable()->after('payment_method');
            $table->string('midtrans_snap_token', 255)->nullable()->after('midtrans_order_id');
            $table->string('midtrans_payment_type', 50)->nullable()->after('midtrans_snap_token');
            $table->string('midtrans_transaction_id', 100)->nullable()->after('midtrans_payment_type');
        });
    }
};
