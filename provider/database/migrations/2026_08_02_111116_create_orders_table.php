<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique(); // e.g., ORD-2038
            $table->foreignId('service_id')->constrained('services')->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('provider_id')->constrained('users')->onDelete('cascade');
            $table->enum('status', ['Negosiasi', 'Diproses', 'Selesai', 'Ditolak'])->default('Negosiasi');
            $table->integer('harga_awal');
            $table->integer('harga_tawaran'); // Harga yang sedang dinegosiasikan
            $table->text('deskripsi_kebutuhan');
            $table->timestamps();
        });
    }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
