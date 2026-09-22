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
        Schema::create('identitas_mapels', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kelas_id')->constrained('kelas')->cascadeOnDelete();
            $table->string('mata_pelajaran');
            $table->string('materi');
            $table->string('satuan_pendidikan');
            $table->string('tahun_pelajaran');
            $table->string('tahapan_fase');
            $table->string('kelas_label');
            $table->string('semester');
            $table->string('alokasi_waktu');
            $table->text('capaian_pembelajaran');
            $table->text('alur_tujuan_pembelajaran');
            $table->json('tujuan_pembelajaran');
            $table->json('indikator_ketercapaian');
            $table->json('model_pembelajaran');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('identitas_mapels');
    }
};
