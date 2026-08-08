<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesanan', function (Blueprint $table) {
            $table->id('id_pesanan');
            $table->foreignId('id_negosiasi')->constrained('negosiasi', 'id_negosiasi')->onDelete('cascade');
            $table->decimal('harga_final', 10, 2);
            $table->timestamp('tanggal_pesanan')->useCurrent();
            $table->enum('status_pesanan', ['menunggu_pengerjaan', 'dikerjakan', 'revisi', 'selesai', 'dibatalkan'])
                  ->default('menunggu_pengerjaan');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesanan');
    }
};
