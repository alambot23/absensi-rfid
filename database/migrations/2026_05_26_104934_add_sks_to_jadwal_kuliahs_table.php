<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('jadwal_kuliah', function (Blueprint $table) {
            // Menambahkan kolom SKS dengan nilai default 3 agar data lama tidak error
            $table->integer('sks')->default(3)->after('kode_matkul');
        });
    }

    public function down(): void
    {
        Schema::table('jadwal_kuliahs', function (Blueprint $table) {
            $table->dropColumn('sks');
        });
    }
};
