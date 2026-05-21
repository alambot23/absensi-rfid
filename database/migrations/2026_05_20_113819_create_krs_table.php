<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('krs', function (Blueprint $table) {
            $table->id();
            
            // Mahasiswa peserta
            $table->foreignId('mahasiswa_id')
                  ->constrained('users')
                  ->onDelete('cascade');
            
            // Jadwal yang diikuti
            $table->foreignId('jadwal_id')
                  ->constrained('jadwal_kuliah')
                  ->onDelete('cascade');
            
            $table->string('semester', 20); // Dibuat wajib (tidak nullable) untuk keperluan unique
            $table->timestamps();
            
            // Perbaikan Logika: Unik berdasarkan Mahasiswa + Jadwal + Semester
            // Ini memungkinkan mahasiswa mengambil jadwal yang sama jika di semester berbeda (mengulang)
            $table->unique(['mahasiswa_id', 'jadwal_id', 'semester']);
            
            // Index tambahan TIDAK PERLU karena unique sudah otomatis membuat index
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('krs');
    }
};