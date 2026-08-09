<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesan_negosiasi', function (Blueprint $table) {
            $table->index(['id_negosiasi', 'created_at'], 'idx_pesan_nego_created');
            $table->index('id_pengirim', 'idx_pesan_pengirim');
        });

        Schema::table('layanan', function (Blueprint $table) {
            $table->index(['id_provider', 'kategori'], 'idx_layanan_prov_kat');
            $table->index('harga', 'idx_layanan_harga');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->index(['id_pesanan', 'status_bayar'], 'idx_pembayaran_pesanan_status');
        });

        Schema::table('penarikan_saldo', function (Blueprint $table) {
            $table->index(['id_provider', 'status'], 'idx_penarikan_prov_status');
        });
    }

    public function down(): void
    {
        Schema::table('pesan_negosiasi', function (Blueprint $table) {
            $table->dropIndex('idx_pesan_nego_created');
            $table->dropIndex('idx_pesan_pengirim');
        });

        Schema::table('layanan', function (Blueprint $table) {
            $table->dropIndex('idx_layanan_prov_kat');
            $table->dropIndex('idx_layanan_harga');
        });

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->dropIndex('idx_pembayaran_pesanan_status');
        });

        Schema::table('penarikan_saldo', function (Blueprint $table) {
            $table->dropIndex('idx_penarikan_prov_status');
        });
    }
};
