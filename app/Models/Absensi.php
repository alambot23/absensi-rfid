<?php
// app/Models/Absensi.php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Absensi extends Model
{
    protected $table = 'absensi';
    
    /**
     * Konstanta Status Kehadiran (Mencegah typo saat insert/update)
     */
    public const STATUS_HADIR = 'hadir';
    public const STATUS_TERLAMBAT = 'terlambat';
    public const STATUS_SAKIT = 'sakit';
    public const STATUS_IZIN = 'izin';
    public const STATUS_ALPA = 'alpa';

    protected $fillable = [
        'mahasiswa_id', 'jadwal_id', 'waktu_tap',
        'status', 'is_manual_override', 'catatan_override',
        'override_by', 'tanggal_kuliah'
    ];

    protected $casts = [
        'waktu_tap' => 'datetime',
        'tanggal_kuliah' => 'date',
        'is_manual_override' => 'boolean',
    ];

    // =====================================================================
    // RELASI
    // =====================================================================

    /**
     * Relasi ke Mahasiswa.
     * Menggunakan withTrashed() agar riwayat absen tetap bisa dibaca
     * meskipun data mahasiswa sudah dihapus (soft delete).
     */
    public function mahasiswa()
    {
        return $this->belongsTo(User::class, 'mahasiswa_id')->withTrashed();
    }

    /**
     * Relasi ke Jadwal Kuliah.
     * Menggunakan withTrashed() untuk alasan yang sama.
     */
    public function jadwal()
    {
        return $this->belongsTo(JadwalKuliah::class, 'jadwal_id')->withTrashed();
    }

    /**
     * FIX: Relasi ke User (Dosen/Admin) yang melakukan manual override.
     */
    public function overrider()
    {
        return $this->belongsTo(User::class, 'override_by')->withTrashed();
    }

    // =====================================================================
    // HELPER / SCOPES (Opsional untuk mempermudah query)
    // =====================================================================

    /**
     * Scope untuk mengambil absensi hari ini saja
     */
    public function scopeHariIni($query)
    {
        return $query->whereDate('tanggal_kuliah', today());
    }
}