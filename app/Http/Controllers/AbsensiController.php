<?php
// app/Http/Controllers/AbsensiController.php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JadwalKuliah;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Inertia\Inertia;
use Carbon\Carbon;

class AbsensiController extends Controller
{
    /**
     * Tampilkan halaman Monitor Ruang Kelas (Frontend Vue)
     */
    public function monitorRuangan(string $ruangan): \Inertia\Response
    {
        // $namaRuangan hanya untuk ditampilkan cantik di layar (contoh: "Lab 2")
        $namaRuangan = str_replace('-', ' ', ucwords($ruangan, '-'));
        
        return Inertia::render('Monitor/RuangKelas', [
            'ruangan' => $namaRuangan,
            'ruanganSlug' => $ruangan, // Tetap kirim slug aslinya (lab-2) ke Vue
        ]);
    }

    /**
     * API: Info jadwal yang sedang aktif di ruangan ini
     */
    public function jadwalAktif(string $ruangan): JsonResponse
    {
        // PERBAIKAN UTAMA: Kita lempar $ruangan (slug) langsung ke aktifSekarang()
        $jadwal = JadwalKuliah::with('dosen:id,name')
            ->aktifSekarang($ruangan) 
            ->first();

        if (!$jadwal) {
            return response()->json([
                'status' => 'tidak_ada_jadwal',
                'message' => 'Tidak ada kelas aktif di ruangan ini.',
                'jadwal' => null,
            ]);
        }

        return response()->json([
            'status' => 'ada_jadwal',
            'jadwal' => [
                'id' => $jadwal->id,
                'mata_kuliah' => $jadwal->mata_kuliah,
                'dosen' => $jadwal->dosen->name,
                'jam_mulai' => $jadwal->jam_mulai,
                'jam_selesai' => $jadwal->jam_selesai,
                'ruangan' => $jadwal->ruangan,
            ],
        ]);
    }

    /**
     * API: Proses tap kartu RFID — INTI SISTEM
     */
    public function prosesAbsensi(Request $request): JsonResponse
    {
        $request->validate([
            'rfid_uid' => 'required|string|min:8|max:64',
            'ruangan'  => 'required|string',
        ]);

        $rfidUid = strtolower(trim($request->rfid_uid));
        $ruanganSlug = $request->ruangan; // Ini adalah slug dari Vue (contoh: 'lab-2')
        $now = Carbon::now();

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // LANGKAH 1: Validasi Kartu RFID
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        $mahasiswa = User::where('rfid_uid', $rfidUid)
            ->where('role', 'mahasiswa')
            ->first();

        if (!$mahasiswa) {
            return response()->json([
                'success' => false,
                'type' => 'kartu_tidak_terdaftar',
                'message' => 'Kartu tidak dikenal. Hubungi admin.',
            ], 404);
        }

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // LANGKAH 2: Cek Jadwal Aktif di Ruangan
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // PERBAIKAN: Gunakan $ruanganSlug agar cocok dengan database
        $jadwal = JadwalKuliah::aktifSekarang($ruanganSlug)->first();

        if (!$jadwal) {
            return response()->json([
                'success' => false,
                'type' => 'tidak_ada_jadwal',
                'message' => 'Tidak ada kelas yang sedang berlangsung di ruangan ini.',
            ], 422);
        }

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // LANGKAH 3: Validasi KRS (Peserta Matkul)
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        $terdaftarKRS = $jadwal->mahasiswaPeserta()
            ->whereKey($mahasiswa->id)
            ->exists();

        if (!$terdaftarKRS) {
            return response()->json([
                'success' => false,
                'type' => 'bukan_peserta',
                'message' => "Anda bukan peserta {$jadwal->mata_kuliah}. Salah masuk kelas?",
                'mahasiswa' => [
                    'nama' => $mahasiswa->name,
                    'nim' => $mahasiswa->nim_nidn,
                ],
            ], 403);
        }

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // LANGKAH 4: Anti-Double Tap (DB Level)
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        $sudahAbsen = Absensi::where('mahasiswa_id', $mahasiswa->id)
            ->where('jadwal_id', $jadwal->id)
            ->whereDate('tanggal_kuliah', $now->toDateString())
            ->exists();

        if ($sudahAbsen) {
            return response()->json([
                'success' => false,
                'type' => 'sudah_absen',
                'message' => 'Anda sudah melakukan absensi untuk kelas ini hari ini.',
                'mahasiswa' => [
                    'nama' => $mahasiswa->name,
                    'nim' => $mahasiswa->nim_nidn,
                    'foto' => $mahasiswa->foto ? asset('storage/' . $mahasiswa->foto) : null,
                ],
            ], 409);
        }

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // LANGKAH 5: Tentukan Status Kehadiran
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        $jamMulai = Carbon::createFromTimeString($jadwal->jam_mulai);
        $batasTerlambat = $jamMulai->copy()->addMinutes($jadwal->toleransi_menit);
        
        $status = $now->gt($batasTerlambat) ? 'terlambat' : 'hadir';

        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        // LANGKAH 6: Simpan ke Database
        // ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━
        $absensi = Absensi::create([
            'mahasiswa_id'   => $mahasiswa->id,
            'jadwal_id'      => $jadwal->id,
            'waktu_tap'      => $now,
            'status'         => $status,
            'tanggal_kuliah' => $now->toDateString(),
        ]);

        return response()->json([
            'success' => true,
            'type' => 'berhasil',
            'status' => $status,
            'message' => $status === 'hadir' 
                ? "Selamat datang, {$mahasiswa->name}!" 
                : "Anda terlambat, {$mahasiswa->name}.",
            'mahasiswa' => [
                'nama'  => $mahasiswa->name,
                'nim'   => $mahasiswa->nim_nidn,
                'foto'  => $mahasiswa->foto ? asset('storage/' . $mahasiswa->foto) : null,
            ],
            'mata_kuliah' => $jadwal->mata_kuliah,
            'waktu_tap'   => $now->format('H:i:s'),
        ]);
    }
}