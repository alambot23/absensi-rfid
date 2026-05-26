<?php
// routes/web.php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AdminMahasiswaController;
use App\Http\Controllers\AdminJadwalController;
use App\Http\Controllers\AdminKrsController;
use App\Http\Controllers\AdminLaporanController; 
use App\Http\Controllers\DashboardController;  
use App\Http\Controllers\AdminDosenController;
use App\Http\Controllers\AdminRuanganController;
use App\Http\Controllers\DosenController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

// =====================================================================
// 1. ROUTE PUBLIC (Halaman Landing Page)
// =====================================================================
Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

// =====================================================================
// 2. ROUTE ADMIN & DOSEN (WAJIB LOGIN)
// =====================================================================
Route::middleware(['auth', 'verified'])->group(function () {
    
    // Dashboard (Bisa diakses Admin & Dosen)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profil User
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // -----------------------------------------------------------------
    // KHUSUS ADMIN (Terkunci untuk role: admin)
    // -----------------------------------------------------------------
    Route::prefix('admin')->middleware('role:admin')->group(function () {
        
        // Mahasiswa
        Route::get('/mahasiswa', [AdminMahasiswaController::class, 'index'])->name('admin.mahasiswa');
        Route::post('/mahasiswa', [AdminMahasiswaController::class, 'store']);
        Route::put('/mahasiswa/{id}', [AdminMahasiswaController::class, 'update']);
        Route::delete('/mahasiswa/{id}', [AdminMahasiswaController::class, 'destroy']);

        // Jadwal
        Route::get('/jadwal', [AdminJadwalController::class, 'index'])->name('admin.jadwal');
        Route::post('/jadwal', [AdminJadwalController::class, 'store']);
        Route::put('/jadwal/{id}', [AdminJadwalController::class, 'update']);
        Route::delete('/jadwal/{id}', [AdminJadwalController::class, 'destroy']);

        // Dosen
        Route::get('/dosen', [AdminDosenController::class, 'index'])->name('admin.dosen');
        Route::post('/dosen', [AdminDosenController::class, 'store']);
        Route::put('/dosen/{id}', [AdminDosenController::class, 'update']);
        Route::delete('/dosen/{id}', [AdminDosenController::class, 'destroy']);

        // KRS
        Route::get('/krs', [AdminKrsController::class, 'index'])->name('admin.krs');
        Route::post('/krs', [AdminKrsController::class, 'store']);
        Route::get('/krs/peserta/{jadwal}', [AdminKrsController::class, 'pesertaKelas']);
        
        // Ruangan & Laporan
        Route::get('/ruangan', [AdminRuanganController::class, 'index'])->name('admin.ruangan');
        Route::get('/laporan', [AdminLaporanController::class, 'index'])->name('admin.laporan');
    });

    // -----------------------------------------------------------------
    // KHUSUS DOSEN (Terkunci untuk role: dosen)
    // -----------------------------------------------------------------
    Route::prefix('dosen')->middleware('role:dosen')->group(function () {
        Route::get('/jadwal', [DosenController::class, 'jadwal'])->name('dosen.jadwal');
        Route::get('/validasi', [DosenController::class, 'validasi'])->name('dosen.validasi');
        Route::put('/validasi/{id}', [DosenController::class, 'updateValidasi']);
        Route::get('/jadwal/export/{id}', [DosenController::class, 'exportLaporan'])->name('dosen.export');
        
    });

}); 

// =====================================================================
// 3. ROUTE APLIKASI ABSENSI (LAYAR KELAS - TANPA LOGIN)
// =====================================================================
Route::get('/absen/{ruangan}', [AbsensiController::class, 'monitorRuangan']);
Route::get('/api/jadwal-aktif/{ruangan}', [AbsensiController::class, 'jadwalAktif']);
Route::post('/proses-absensi', [AbsensiController::class, 'prosesAbsensi']);

// =====================================================================
// 4. ROUTE AUTENTIKASI BREEZE
// =====================================================================
require __DIR__.'/auth.php';