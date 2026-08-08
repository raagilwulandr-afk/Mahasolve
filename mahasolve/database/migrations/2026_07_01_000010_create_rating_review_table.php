<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rating_review', function (Blueprint $table) {
            $table->id('id_review');
            $table->foreignId('id_pesanan')->constrained('pesanan', 'id_pesanan')->onDelete('cascade');
            $table->text('review')->nullable();
            $table->tinyInteger('rate')->unsigned();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rating_review');
    }
};
