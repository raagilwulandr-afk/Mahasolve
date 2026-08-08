<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tracking_pesanan', function (Blueprint $table) {
            $table->id('id_tracking');
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->onDelete('cascade');
            $table->string('status_pengerjaan', 150)->nullable();
            $table->string('file_progress')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tracking_pesanan');
    }
};
