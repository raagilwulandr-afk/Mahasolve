<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('negosiasi', function (Blueprint $table) {
            $table->id('id_negosiasi');
            $table->foreignId('id_request')->constrained('request_layanan', 'id_request')->onDelete('cascade');
            $table->foreignId('id_provider')->constrained('provider', 'id_provider')->onDelete('cascade');
            $table->decimal('harga_tawaran', 10, 2);
            $table->text('detail_negosiasi')->nullable();
            $table->enum('status_negosiasi', ['pending', 'ditawar_ulang', 'disepakati', 'ditolak'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('negosiasi');
    }
};
