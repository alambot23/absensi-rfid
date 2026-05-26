<?php

namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use App\Models\Absensi;
use App\Models\User; // <--- SUDAH SAYA GANTI MENJADI USER
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class DosenController extends Controller
{
    public function jadwal()
    {
        $jadwal = JadwalKuliah::where('dosen_id', auth()->id())
            ->withCount('mahasiswaPeserta') 
            ->get();

        return Inertia::render('Dosen/Jadwal', [
            'daftarJadwal' => $jadwal
        ]);
    }

    public function validasi()
    {
        $dosenId = auth()->id();
        $jadwalIds = JadwalKuliah::where('dosen_id', $dosenId)->pluck('id');

        $absensiHariIni = Absensi::with(['mahasiswa:id,name,nim_nidn', 'jadwal:id,mata_kuliah,ruangan'])
            ->whereIn('jadwal_id', $jadwalIds)
            ->whereDate('tanggal_kuliah', Carbon::today())
            ->orderBy('waktu_tap', 'desc')
            ->get();

        return Inertia::render('Dosen/Validasi', [
            'dataAbsensi' => $absensiHariIni
        ]);
    }

    public function updateValidasi(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:hadir,terlambat,sakit,izin,alpa',
        ]);

        $absensi = Absensi::findOrFail($id);
        $absensi->update([
            'status' => $request->status,
            'is_manual_override' => true,
            'catatan_override' => 'Diubah manual oleh Dosen',
            'override_by' => auth()->id(),
        ]);

        return redirect()->back()->with('success', 'Status absensi berhasil diperbarui!');
    }

    public function exportLaporan($id)
    {
        // 1. Ambil Jadwal & Dosen
        $jadwal = JadwalKuliah::with('dosen')->where('id', $id)
            ->where('dosen_id', auth()->id())->firstOrFail();

        // 2. Ambil seluruh riwayat absensi untuk jadwal ini
        $semuaAbsensi = Absensi::with('mahasiswa')
            ->where('jadwal_id', $id)
            ->orderBy('tanggal_kuliah', 'asc')
            ->get();

        // 3. Susun array tanggal pertemuan (Otomatis dari data absensi)
        $tanggalUnik = $semuaAbsensi->pluck('tanggal_kuliah')->map(function($date) {
            return Carbon::parse($date)->format('Y-m-d');
        })->unique()->values();

        $pertemuan = [];
        foreach ($tanggalUnik as $index => $tgl) {
            $pertemuan[$index + 1] = Carbon::parse($tgl)->format('d-m');
        }

        // 4. Ambil daftar mahasiswa yang unik dari tabel absensi
        $mahasiswaIds = $semuaAbsensi->pluck('mahasiswa_id')->unique();
        
        // ---> MENGGUNAKAN MODEL USER <---
        $peserta = User::whereIn('id', $mahasiswaIds)->orderBy('nim_nidn', 'asc')->get();

        // 5. Petakan status absensi tiap mahasiswa ke kolom 1 sampai 16
        foreach ($peserta as $mhs) {
            $absensiMhs = [];
            for ($i = 1; $i <= 16; $i++) {
                if (isset($tanggalUnik[$i - 1])) {
                    // Cari absen mahasiswa ini di tanggal tersebut
                    $record = $semuaAbsensi->where('mahasiswa_id', $mhs->id)
                                           ->where('tanggal_kuliah', $tanggalUnik[$i - 1])
                                           ->first();
                    
                    if ($record) {
                        $status = strtolower($record->status);
                        if (in_array($status, ['hadir', 'terlambat'])) $absensiMhs[$i] = 'H';
                        elseif ($status == 'sakit') $absensiMhs[$i] = 'S';
                        elseif ($status == 'izin') $absensiMhs[$i] = 'I';
                        else $absensiMhs[$i] = 'A';
                    } else {
                        $absensiMhs[$i] = 'A'; 
                    }
                } else {
                    $absensiMhs[$i] = ''; 
                }
            }
            $mhs->absensi = $absensiMhs;
        }

        // 6. Generate PDF menggunakan DomPDF
        $pdf = Pdf::loadView('reports.absensi', compact(
            'jadwal', 
            'peserta', 
            'pertemuan'
        ))->setPaper('a4', 'landscape');

        $namaFile = "Laporan_Absensi_" . str_replace(' ', '_', $jadwal->mata_kuliah) . ".pdf";

        return $pdf->download($namaFile);
    }
}