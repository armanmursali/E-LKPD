<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lkpd_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->unsignedInteger('kegiatan_nomor');
            $table->string('nama_peserta');
            $table->string('tipe_peserta')->default('Individu');
            $table->json('jawaban')->nullable();
            $table->timestamps();
            $table->index(['kelas_id', 'kegiatan_nomor']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('lkpd_submissions');
    }
};