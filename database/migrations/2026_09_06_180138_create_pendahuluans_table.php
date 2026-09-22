<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('pendahuluans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->string('judul');
            $table->string('mapel');
            $table->string('jenjang');
            $table->string('kelas_label');
            $table->string('fase');
            $table->string('kurikulum');
            $table->string('penulis');
            $table->string('pembimbing')->nullable();
            $table->string('validator_media')->nullable();
            $table->string('validator_materi')->nullable();
            $table->json('kata_pengantar');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pendahuluans');
    }
};
