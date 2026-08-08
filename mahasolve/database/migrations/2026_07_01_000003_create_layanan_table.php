<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('layanan', function (Blueprint $table) {
            $table->id('id_layanan');
            $table->foreignId('id_provider')->constrained('provider', 'id_provider')->onDelete('cascade');
            $table->string('nama_layanan', 150);
            $table->string('kategori', 100);
            $table->text('deskripsi')->nullable();
            $table->decimal('harga', 10, 2);
            $table->string('estimasi_pengerjaan', 50)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('layanan');
    }
};
