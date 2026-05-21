<?php

use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;

// =====================================================================
// ROUTE HARDWARE (IoT/RFID)
// =====================================================================

// Endpoint ini ditembak langsung oleh alat RFID saat kartu di-tap.
// Sifatnya stateless dan tidak memerlukan token CSRF.
Route::post('/proses-absensi', [AbsensiController::class, 'prosesAbsensi']);