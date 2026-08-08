<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detail_pekerjaan', function (Blueprint $table) {
            $table->id('id_detail');
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->onDelete('cascade');
            $table->string('dokumen')->nullable();
            $table->text('instruksi_pengerjaan')->nullable();
            $table->string('referensi')->nullable();
            $table->string('format_hasil', 50)->nullable();
            $table->timestamp('tanggal_upload')->useCurrent();
            $table->enum('status', ['lengkap', 'menunggu_kelengkapan'])->default('lengkap');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detail_pekerjaan');
    }
};
