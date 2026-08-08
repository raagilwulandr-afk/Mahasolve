<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('provider', function (Blueprint $table) {
            $table->string('status_verifikasi', 50)->default('terverifikasi')->after('detail_provider');
            $table->string('nomor_ktm', 50)->nullable()->after('status_verifikasi');
            $table->string('nomor_sim', 50)->nullable()->after('nomor_ktm');
        });
    }

    public function down(): void
    {
        Schema::table('provider', function (Blueprint $table) {
            $table->dropColumn(['status_verifikasi', 'nomor_ktm', 'nomor_sim']);
        });
    }
};
