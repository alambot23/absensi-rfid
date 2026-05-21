<?php

namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use App\Models\Absensi;
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
    $jadwal = JadwalKuliah::with('dosen')->where('id', $id)
        ->where('dosen_id', auth()->id())->firstOrFail();

    $absensi = Absensi::with('mahasiswa')
        ->where('jadwal_id', $id)
        ->get();

    // Render view ke PDF
    $pdf = Pdf::loadView('reports.absensi', compact('jadwal', 'absensi'));

    return $pdf->download("Laporan_Absensi_" . $jadwal->mata_kuliah . ".pdf");
}
}