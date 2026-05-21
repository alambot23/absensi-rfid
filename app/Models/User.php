<?php

namespace App\Models;

// Import class dan trait bawaan Laravel
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
// use Laravel\Sanctum\HasApiTokens; // Uncomment baris ini jika Anda membuat API (menggunakan token)

class User extends Authenticatable
{
    // Gunakan trait yang sudah di-import di atas
    use HasFactory, Notifiable, SoftDeletes;
    // use HasApiTokens; // Uncomment ini juga jika membuat API

    /**
     * Kolom-kolom yang boleh diisi secara massal (Mass Assignment).
     */
    protected $fillable = [
        'name', 
        'email', 
        'password', 
        'nim_nidn', 
        'rfid_uid', 
        'role', 
        'foto'
    ];

    /**
     * Kolom-kolom yang disembunyikan saat model diubah menjadi array atau JSON.
     */
    protected $hidden = [
        'password', 
        'remember_token',
    ];

    /**
     * Casting tipe data secara otomatis (opsional tapi sangat disarankan).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Gunakan ini jika Anda memakai Laravel 10 ke atas
    ];

    // =====================================================================
    // RELASI UNTUK DOSEN
    // =====================================================================

    /**
     * Relasi: Dosen memiliki banyak jadwal mengajar.
     */
    public function jadwalMengajar()
    {
        return $this->hasMany(JadwalKuliah::class, 'dosen_id');
    }

    // =====================================================================
    // RELASI UNTUK MAHASISWA
    // =====================================================================

    /**
     * Relasi: Mahasiswa memiliki banyak data KRS.
     */
    public function krs()
    {
        return $this->hasMany(Krs::class, 'mahasiswa_id');
    }

    /**
     * Relasi: Jadwal Kuliah (Many-to-Many) yang diambil mahasiswa via tabel KRS.
     */
    public function jadwalKuliah()
    {
        return $this->belongsToMany(
            JadwalKuliah::class, 
            'krs', 
            'mahasiswa_id', 
            'jadwal_id'
        );
    }

    /**
     * Relasi: Riwayat absensi mahasiswa.
     */
    public function absensi()
    {
        return $this->hasMany(Absensi::class, 'mahasiswa_id');
    }

    // =====================================================================
    // HELPER METHODS (UNTUK PENGECEKAN ROLE)
    // =====================================================================

    /**
     * Cek apakah user adalah mahasiswa
     */
    public function isMahasiswa(): bool
    {
        return $this->role === 'mahasiswa';
    }

    /**
     * Cek apakah user adalah dosen
     */
    public function isDosen(): bool
    {
        return $this->role === 'dosen';
    }

    /**
     * Cek apakah user adalah admin (Tambahan, sesuaikan jika ada role admin)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}