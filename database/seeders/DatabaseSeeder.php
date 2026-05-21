<?php

namespace Database\Seeders; // 1. Wajib ada namespace

use Illuminate\Database\Seeder; // 2. Wajib import class Seeder
use App\Models\User;
use App\Models\JadwalKuliah;
use App\Models\Krs;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin
        User::create([
            'name' => 'Administrator',
            'email' => 'admin@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'nim_nidn' => 'ADM001',
        ]);

        // Dosen
        $dosen = User::create([
            'name' => 'Dr. Budi Santoso',
            'email' => 'budi@kampus.ac.id',
            'password' => Hash::make('password'),
            'role' => 'dosen',
            'nim_nidn' => '0123456789',
        ]);

        // Mahasiswa dengan kartu RFID
        $mhs1 = User::create([
            'name' => 'Andi Pratama',
            'email' => 'andi@mahasiswa.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'nim_nidn' => '2021001001',
            'rfid_uid' => '3374b291',   // 8 char — kartu putih
        ]);

        $mhs2 = User::create([
            'name' => 'Siti Rahayu',
            'email' => 'siti@mahasiswa.ac.id',
            'password' => Hash::make('password'),
            'role' => 'mahasiswa',
            'nim_nidn' => '2021001002',
            'rfid_uid' => 'a3424fa2',  // 16 char — key fob kuning
        ]);

        // =====================================================================
        // JADWAL (Disetel aktif untuk testing malam ini)
        // =====================================================================
        $jadwal = JadwalKuliah::create([
            'mata_kuliah' => 'Pemrograman Web',
            'kode_matkul' => 'TIF301',
            'ruangan' => 'Ruang 301', // FIX: Tanpa strip agar cocok dengan parse Controller
            'hari' => 'Rabu',
            'jam_mulai' => '18:00:00', // FIX: Diubah ke jam 6 sore
            'jam_selesai' => '21:30:00', // FIX: Sampai setengah 10 malam
            'toleransi_menit' => 15,
            'dosen_id' => $dosen->id,
            'semester' => 'Genap 2025/2026', // Disesuaikan dengan tahun akademik
        ]);

        // =====================================================================
        // KRS (Penambahan kolom semester)
        // =====================================================================
        Krs::create([
            'mahasiswa_id' => $mhs1->id, 
            'jadwal_id' => $jadwal->id,
            'semester' => 'Genap 2025/2026' // FIX: Kolom wajib diisi
        ]);
        
        Krs::create([
            'mahasiswa_id' => $mhs2->id, 
            'jadwal_id' => $jadwal->id,
            'semester' => 'Genap 2025/2026' // FIX: Kolom wajib diisi
        ]);
    }
}