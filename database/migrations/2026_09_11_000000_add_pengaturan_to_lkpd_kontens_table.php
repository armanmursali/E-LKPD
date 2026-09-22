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
        Schema::table('lkpd_kontens', function (Blueprint $table) {
            $table->json('pengaturan')->nullable()->after('blocks');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lkpd_kontens', function (Blueprint $table) {
            $table->dropColumn('pengaturan');
        });
    }
};
