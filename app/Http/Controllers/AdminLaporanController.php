<?php

namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use App\Models\Absensi;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Carbon\Carbon;

class AdminLaporanController extends Controller
{
    public function index(Request $request)
    {
        // Tangkap parameter pencarian dari frontend (default hari ini)
        $jadwalId = $request->query('jadwal_id');
        $tanggal = $request->query('tanggal', Carbon::today()->toDateString());

        // Ambil data untuk dropdown pilihan jadwal
        $daftarJadwal = JadwalKuliah::with('dosen:id,name')->latest()->get();

        $laporan = [];

        // Jika Admin sudah memilih kelas, generate laporan:
        if ($jadwalId) {
            $jadwal = JadwalKuliah::findOrFail($jadwalId);
            
            // 1. Ambil SEMUA mahasiswa yang sah terdaftar di kelas tersebut (dari KRS)
            $mahasiswaKrs = $jadwal->mahasiswaPeserta()->orderBy('name')->get();

            foreach ($mahasiswaKrs as $mhs) {
                // 2. Cek apakah mahasiswa ini melakukan tap RFID di tanggal tersebut
                $absen = Absensi::where('mahasiswa_id', $mhs->id)
                    ->where('jadwal_id', $jadwalId)
                    ->whereDate('tanggal_kuliah', $tanggal)
                    ->first();

                // 3. Susun data. Jika tidak ada log absen, otomatis statusnya "Alpa"
                $laporan[] = [
                    'id' => $mhs->id,
                    'nim_nidn' => $mhs->nim_nidn,
                    'name' => $mhs->name,
                    'status' => $absen ? $absen->status : 'alpa',
                    'waktu_tap' => $absen ? $absen->waktu_tap->format('H:i:s') : '-',
                ];
            }
        }

        return Inertia::render('Admin/Laporan', [
            'daftarJadwal' => $daftarJadwal,
            'laporan' => $laporan,
            'filters' => [
                'jadwal_id' => $jadwalId,
                'tanggal' => $tanggal,
            ]
        ]);
    }
}