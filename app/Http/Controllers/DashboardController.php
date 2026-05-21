<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JadwalKuliah;
use App\Models\Absensi;
use Inertia\Inertia;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $hariIni = Carbon::today()->toDateString();

        // 1. Hitung Statistik Utama
        $totalMahasiswa = User::where('role', 'mahasiswa')->count();
        $totalJadwal = JadwalKuliah::count();
        $absenHariIni = Absensi::whereDate('tanggal_kuliah', $hariIni)->count();

        // 2. Ambil 5 aktivitas tap RFID paling baru hari ini
        $riwayatTerbaru = Absensi::with(['mahasiswa:id,name,nim_nidn', 'jadwal:id,mata_kuliah'])
            ->whereDate('tanggal_kuliah', $hariIni)
            ->latest() // Urutkan dari yang paling baru
            ->take(5)
            ->get()
            ->map(function ($absen) {
                return [
                    'id' => $absen->id,
                    'nama_mahasiswa' => $absen->mahasiswa->name ?? 'Tidak diketahui',
                    'nim' => $absen->mahasiswa->nim_nidn ?? '-',
                    'mata_kuliah' => $absen->jadwal->mata_kuliah ?? '-',
                    'status' => $absen->status,
                    'waktu' => $absen->waktu_tap->format('H:i:s'),
                ];
            });

        // 3. Kirim data ke Vue
        return Inertia::render('Dashboard', [
            'stats' => [
                'total_mahasiswa' => $totalMahasiswa,
                'total_jadwal' => $totalJadwal,
                'absen_hari_ini' => $absenHariIni,
            ],
            'riwayat_terbaru' => $riwayatTerbaru,
        ]);
    }
}