<?php
// database/migrations/xxxx_create_jadwal_kuliah_table.php
use Illuminate\Database\Migrations\Migration; // Ini yang hilang!
use Illuminate\Database\Schema\Blueprint;    // Ini juga perlu
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jadwal_kuliah', function (Blueprint $table) {
            $table->id();
            $table->string('mata_kuliah', 100);
            $table->string('kode_matkul', 20)->nullable();
            $table->string('ruangan', 50);  // Contoh: "Ruang-301", "Lab-A"
            $table->enum('hari', ['Senin','Selasa','Rabu','Kamis','Jumat','Sabtu']);
            $table->time('jam_mulai');
            $table->time('jam_selesai');
            $table->integer('toleransi_menit')->default(15); // Batas terlambat, fleksibel per matkul
            
            // Dosen pengajar (FK ke users)
            $table->foreignId('dosen_id')
                  ->constrained('users')
                  ->onDelete('restrict'); // Dosen tidak bisa dihapus jika ada jadwal
            
            // Status kunci sesi (dosen bisa lock absensi)
            $table->boolean('is_locked')->default(false);
            $table->string('semester', 20)->nullable(); // Contoh: "Ganjil 2024/2025"
            
            $table->timestamps();
            $table->softDeletes();
            
            // Index untuk query cepat berdasarkan ruangan + hari + jam
            $table->index(['ruangan', 'hari', 'jam_mulai', 'jam_selesai']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jadwal_kuliah');
    }
};