<?php
namespace App\Http\Controllers;

use App\Models\JadwalKuliah;
use Inertia\Inertia;

class AdminRuanganController extends Controller
{
    public function index()
    {
        // Ambil daftar ruangan unik dari tabel jadwal
        $ruangan = JadwalKuliah::distinct()->pluck('ruangan');
        
        return Inertia::render('Admin/Ruangan', [
            'daftarRuangan' => $ruangan
        ]);
    }
}