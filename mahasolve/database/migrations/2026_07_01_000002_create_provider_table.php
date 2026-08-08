<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('provider', function (Blueprint $table) {
            $table->id('id_provider');
            $table->foreignId('id_user')->constrained('user', 'id_user')->onDelete('cascade');
            $table->decimal('rating', 2, 1)->default(0.0);
            $table->text('detail_provider')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('provider');
    }
};
