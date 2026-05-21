<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('absensi', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('mahasiswa_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('jadwal_id')->constrained('jadwal_kuliah')->onDelete('cascade');
            
            $table->timestamp('waktu_tap')->nullable(); // Nullable jika statusnya Alpa (input sistem)
            $table->enum('status', ['hadir', 'terlambat', 'alpa', 'izin', 'sakit'])->default('hadir');
            
            $table->boolean('is_manual_override')->default(false);
            $table->text('catatan_override')->nullable();
            $table->foreignId('override_by')->nullable()->constrained('users')->onDelete('set null');
            
            $table->date('tanggal_kuliah');
            $table->timestamps();
            
            $table->unique(['mahasiswa_id', 'jadwal_id', 'tanggal_kuliah'], 'unique_absensi_mahasiswa');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('absensi');
    }
};