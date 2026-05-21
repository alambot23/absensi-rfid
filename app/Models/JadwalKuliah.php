<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class JadwalKuliah extends Model
{
    use SoftDeletes;

    protected $table = 'jadwal_kuliah';

    protected $fillable = [
        'mata_kuliah',
        'kode_matkul',
        'ruangan',
        'ruangan_slug',
        'hari',
        'jam_mulai',
        'jam_selesai',
        'toleransi_menit',
        'dosen_id',
        'is_locked',
        'semester'
    ];

    protected $casts = [
        'is_locked' => 'boolean',
    ];

    // =====================================================
    // RELASI DATABASE
    // =====================================================

    public function dosen()
    {
        return $this->belongsTo(User::class, 'dosen_id');
    }

    public function mahasiswaPeserta()
    {
        return $this->belongsToMany(
            User::class,
            'krs',
            'jadwal_id',
            'mahasiswa_id'
        );
    }

    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'jadwal_id');
    }

    // =====================================================
    // SCOPE PENCARIAN JADWAL AKTIF
    // =====================================================

    /**
     * Scope untuk mencari jadwal yang sedang berlangsung SAAT INI
     * berdasarkan hari, jam, dan slug ruangan.
     */
    public function scopeAktifSekarang($query, string $ruanganSlug)
    {
        // Carbon::now() akan mengambil waktu saat ini sesuai konfigurasi timezone di config/app.php
        $now = Carbon::now();

        // Mapping angka hari dari Carbon (0 = Minggu, 1 = Senin, dst) ke Bahasa Indonesia
        $namaHari = [
            'Minggu',
            'Senin',
            'Selasa',
            'Rabu',
            'Kamis',
            'Jumat',
            'Sabtu'
        ];

        // Dapatkan nama hari ini (Misal: 'Kamis')
        $hari = $namaHari[$now->dayOfWeek];

        // Dapatkan jam saat ini dalam format H:i:s (Misal: '00:45:10')
        $jamSekarang = $now->format('H:i:s');

        return $query
            ->where('ruangan_slug', $ruanganSlug)      // Cocokkan slug (contoh: 'lab-2')
            ->where('hari', $hari)                     // Cocokkan hari (contoh: 'Kamis')
            ->whereTime('jam_mulai', '<=', $jamSekarang) // Kelas sudah mulai
            ->whereTime('jam_selesai', '>=', $jamSekarang) // Kelas belum selesai
            ->where('is_locked', false);               // Kelas tidak dikunci oleh admin
    }
}