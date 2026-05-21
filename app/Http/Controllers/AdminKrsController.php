<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JadwalKuliah;
use App\Models\Krs;
use Illuminate\Http\Request;
use Inertia\Inertia;

class AdminKrsController extends Controller
{
    /**
     * Tampilkan halaman utama Manajemen Plotting KRS
     */
    public function index()
    {
        // Ambil semua jadwal kuliah beserta informasi dosen dan jumlah peserta saat ini
        $jadwal = JadwalKuliah::with('dosen:id,name')
            ->withCount('mahasiswaPeserta')
            ->latest()
            ->get();

        // Ambil daftar semua mahasiswa aktif untuk dipilih di form
        $mahasiswa = User::where('role', 'mahasiswa')
            ->orderBy('name', 'asc')
            ->get(['id', 'name', 'nim_nidn']);

        return Inertia::render('Admin/Krs', [
            'daftarJadwal' => $jadwal,
            'daftarMahasiswa' => $mahasiswa,
        ]);
    }

    /**
     * Simpan plot mahasiswa ke dalam kelas/jadwal tertentu
     */
    public function store(Request $request)
    {
        $request->validate([
            'jadwal_id' => 'required|exists:jadwal_kuliah,id',
            'mahasiswa_ids' => 'required|array',
            'mahasiswa_ids.*' => 'required|exists:users,id',
        ]);

        $jadwal = JadwalKuliah::findOrFail($request->jadwal_id);

        foreach ($request->mahasiswa_ids as $mhsId) {
            // Gunakan firstOrCreate untuk menghindari error duplicate entry jika admin tidak sengaja klik dua kali
            Krs::firstOrCreate([
                'mahasiswa_id' => $mhsId,
                'jadwal_id'    => $jadwal->id,
                'semester'     => $jadwal->semester, // Semester otomatis menyamakan dengan master jadwal
            ]);
        }

        return redirect()->back()->with('success', 'Mahasiswa berhasil didaftarkan ke dalam kelas!');
    }

    /**
     * API pendukung: Mengambil data peserta yang sudah terdaftar di suatu kelas (diakses via Axios)
     */
    public function pesertaKelas($jadwalId)
    {
        $jadwal = JadwalKuliah::with('mahasiswaPeserta:id,name,nim_nidn,rfid_uid')->findOrFail($jadwalId);
        return response()->json($jadwal->mahasiswaPeserta);
    }
}