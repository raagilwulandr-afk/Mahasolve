<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penarikan_saldo', function (Blueprint $table) {
            $table->id('id_penarikan');
            $table->foreignId('id_provider')->constrained('provider', 'id_provider')->onDelete('cascade');
            $table->decimal('jumlah', 12, 2);
            $table->string('metode');
            $table->string('no_rekening');
            $table->enum('status', ['diproses', 'disetujui', 'ditolak'])->default('diproses');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penarikan_saldo');
    }
};
