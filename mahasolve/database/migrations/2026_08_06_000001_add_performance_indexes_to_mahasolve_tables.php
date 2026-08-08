<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pesanan', function (Blueprint $table) {
            $table->index(['id_negosiasi', 'status_pesanan'], 'idx_pesanan_nego_status');
        });

        Schema::table('negosiasi', function (Blueprint $table) {
            $table->index(['id_provider', 'status_negosiasi'], 'idx_nego_prov_status');
            $table->index(['id_request', 'status_negosiasi'], 'idx_nego_req_status');
        });

        Schema::table('request_layanan', function (Blueprint $table) {
            $table->index(['id_user', 'status_request'], 'idx_req_user_status');
            $table->index(['kategori', 'status_request'], 'idx_req_kat_status');
        });

        Schema::table('rating_review', function (Blueprint $table) {
            $table->index('id_pesanan', 'idx_review_pesanan');
        });
    }

    public function down(): void
    {
        Schema::table('pesanan', fn (Blueprint $t) => $t->dropIndex('idx_pesanan_nego_status'));
        Schema::table('negosiasi', fn (Blueprint $t) => $t->dropIndex('idx_nego_prov_status'));
        Schema::table('negosiasi', fn (Blueprint $t) => $t->dropIndex('idx_nego_req_status'));
        Schema::table('request_layanan', fn (Blueprint $t) => $t->dropIndex('idx_req_user_status'));
        Schema::table('request_layanan', fn (Blueprint $t) => $t->dropIndex('idx_req_kat_status'));
        Schema::table('rating_review', fn (Blueprint $t) => $t->dropIndex('idx_review_pesanan'));
    }
};
