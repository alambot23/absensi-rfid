<?php
// app/Models/Krs.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Krs extends Model
{
    // Pastikan Laravel membaca tabel 'krs', bukan 'krs_s'
    protected $table = 'krs';

    /**
     * Sesuai dengan migrasi, hanya ada 3 kolom ini yang bisa diisi.
     * (Kolom status dan catatan dihapus agar sesuai dengan database)
     */
    protected $fillable = [
        'mahasiswa_id', 
        'jadwal_id', 
        'semester'
    ];

    // =====================================================================
    // RELASI
    // =====================================================================

    /**
     * Relasi ke Mahasiswa (User).
     * Menggunakan withTrashed() untuk mengantisipasi data user yang di-soft delete.
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id')->withTrashed();
    }

    /**
     * Relasi ke Jadwal Kuliah.
     * Menggunakan withTrashed() untuk mengantisipasi jadwal yang di-soft delete.
     */
    public function jadwal()
    {
        return $this->belongsTo(JadwalKuliah::class, 'jadwal_id')->withTrashed();
    }
}