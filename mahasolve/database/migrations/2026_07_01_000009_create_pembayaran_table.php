<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id('id_pembayaran');
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->onDelete('cascade');
            $table->string('metode_pembayaran', 50)->nullable();
            $table->decimal('total_bayar', 10, 2);
            $table->string('bukti_pembayaran')->nullable();
            $table->enum('status_bayar', ['menunggu_konfirmasi', 'dikonfirmasi', 'ditolak'])->default('menunggu_konfirmasi');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
