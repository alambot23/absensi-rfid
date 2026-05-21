<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\JadwalKuliah;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Inertia\Inertia;

class AdminJadwalController extends Controller
{
    public function index()
    {
        $jadwal = JadwalKuliah::with('dosen:id,name')
            ->latest()
            ->get();

        $dosen = User::where('role', 'dosen')
            ->get(['id', 'name']);

        return Inertia::render('Admin/Jadwal', [
            'daftarJadwal' => $jadwal,
            'daftarDosen' => $dosen,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'mata_kuliah'     => 'required|string|max:255',
            'kode_matkul'     => 'required|string|max:50',
            'ruangan'         => 'required|string|max:50',
            'hari'            => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai'       => 'required|date_format:H:i', // Format fleksibel tanpa detik
            'jam_selesai'     => 'required|date_format:H:i|after:jam_mulai',
            'toleransi_menit' => 'required|integer|min:0',
            'dosen_id'        => 'required|exists:users,id',
            'semester'        => 'required|string|max:50',
        ]);

        // Gabungkan detik secara manual agar sesuai dengan tipe data TIME di MySQL
        $validated['jam_mulai'] = $validated['jam_mulai'] . ':00';
        $validated['jam_selesai'] = $validated['jam_selesai'] . ':00';

        // Buat slug ruangan otomatis
        $validated['ruangan_slug'] = Str::slug($validated['ruangan']);

        JadwalKuliah::create($validated);

        return redirect()
            ->back()
            ->with('success', 'Jadwal berhasil ditambahkan');
    }

    // Fungsi Edit Jadwal (SUDAH DI-FIX)
    public function update(Request $request, $id)
    {
        $jadwal = JadwalKuliah::findOrFail($id);

        $validated = $request->validate([
            'mata_kuliah'     => 'required|string|max:255',
            'kode_matkul'     => 'required|string|max:50',
            'ruangan'         => 'required|string|max:50',
            'hari'            => 'required|string|in:Senin,Selasa,Rabu,Kamis,Jumat,Sabtu,Minggu',
            'jam_mulai'       => 'required|date_format:H:i', // FIX: Diubah ke H:i agar tidak menuntut detik dari frontend
            'jam_selesai'     => 'required|date_format:H:i|after:jam_mulai', // FIX: Diubah ke H:i
            'toleransi_menit' => 'required|integer|min:0',
            'dosen_id'        => 'required|exists:users,id',
            'semester'        => 'required|string|max:50',
        ]);

        // FIX: Tambahkan format detik manual sebelum eksekusi update ke database
        $validated['jam_mulai'] = $validated['jam_mulai'] . ':00';
        $validated['jam_selesai'] = $validated['jam_selesai'] . ':00';

        // Buat slug ruangan otomatis agar konsisten
        $validated['ruangan_slug'] = Str::slug($validated['ruangan']);

        $jadwal->update($validated);

        return redirect()
            ->back()
            ->with('success', 'Jadwal berhasil diperbarui');
    }

    // Fungsi Hapus Jadwal
    public function destroy($id)
    {
        $jadwal = JadwalKuliah::findOrFail($id);
        $jadwal->delete();

        return redirect()
            ->back()
            ->with('success', 'Jadwal Kuliah berhasil dihapus!');
    }
}