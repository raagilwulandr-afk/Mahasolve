<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pesan_negosiasi', function (Blueprint $table) {
            $table->id('id_pesan');
            $table->foreignId('id_negosiasi')->constrained('negosiasi', 'id_negosiasi')->onDelete('cascade');
            $table->foreignId('id_pengirim')->constrained('user', 'id_user')->onDelete('cascade');
            $table->enum('peran_pengirim', ['mahasiswa', 'provider']);
            $table->text('pesan');
            $table->decimal('harga_tawaran', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pesan_negosiasi');
    }
};
