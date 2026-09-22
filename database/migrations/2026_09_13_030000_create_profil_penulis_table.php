<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('profil_penulis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->unique()->constrained('kelas')->cascadeOnDelete();
            $table->text('paragraf');
            $table->string('gambar')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('profil_penulis');
    }
};