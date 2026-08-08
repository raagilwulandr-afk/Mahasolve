<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('request_layanan', function (Blueprint $table) {
            $table->id('id_request');
            $table->foreignId('id_user')->constrained('user', 'id_user')->onDelete('cascade');
            $table->text('detail_kebutuhan');
            $table->string('kategori', 100);
            $table->decimal('harga_awal', 10, 2)->nullable();
            $table->date('deadline')->nullable();
            $table->timestamp('tanggal_request')->useCurrent();
            $table->enum('status_request', ['open', 'diproses', 'selesai', 'dibatalkan'])->default('open');
            $table->text('kriteria_output')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('request_layanan');
    }
};
