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
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            // Menghubungkan ke tabel users (jika user dihapus, layanannya ikut terhapus)
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_layanan');
            $table->string('kategori');
            $table->decimal('harga', 12, 2);
            $table->text('deskripsi');
            $table->string('status')->default('aktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};