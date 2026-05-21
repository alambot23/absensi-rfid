<div align="center">

  <img src="https://cdn-icons-png.flaticon.com/512/8654/8654302.png" alt="NexusApp Logo" width="120" height="120" />

  # 🚀 NexusApp — Sistem Absensi RFID & Akademik Terintegrasi
  
  **"Sistem Manajemen Presensi & Akademik Kampus Kelas Enterprise dengan Antarmuka UI/UX Premium Ala Startup SaaS Modern."**

  ---

  [![Laravel Version](https://img.shields.io/badge/Laravel-v11.x-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
  [![Vue Version](https://img.shields.io/badge/Vue.js-v3.x-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
  [![InertiaJS](https://img.shields.io/badge/Inertia.js-v1.x-9553E8?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
  [![Tailwind CSS](https://img.shields.io/badge/Tailwind_CSS-v3.x-38B2AC?style=for-the-badge&logo=tailwind-css&logoColor=white)](https://tailwindcss.com)
  [![PHP Version](https://img.shields.io/badge/PHP-v8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://php.net)

</div>

## 📌 Tentang Proyek
**NexusApp** adalah platform sistem informasi manajemen absensi perkuliahan berbasis **Kartu RFID (Radio Frequency Identification)** yang dirancang untuk mendigitalkan proses pencatatan kehadiran di ruang kelas secara *real-time*. Menggunakan arsitektur modern perpaduan **Laravel 11**, **Inertia.js**, dan **Vue 3 (Vite)**, aplikasi ini menawarkan performa super cepat berbasis *Single Page Application* (SPA) dengan desain visual dashboard yang sangat memukau.

---

## ✨ Fitur Unggulan (UI Gacor)

### 👑 Modul Administrator (Pusat Komando)
* **Dashboard Statistik Dinamis:** Dilengkapi visualisasi grafik garis tren kehadiran mahasiswa berbasis **Chart.js** dengan gradasi warna premium.
* **Manajemen Data Master Terpusat:** Sistem CRUD mutakhir untuk mengelola data Mahasiswa, Dosen, Ruangan, dan Mata Kuliah.
* **Plotting KRS Fleksibel:** Kemudahan dalam memasukkan dan memetakan mahasiswa ke dalam kelas/jadwal tertentu secara massal.

### 👨‍🏫 Modul Dosen Pengampu
* **Akses Cepat Pengajaran:** Deteksi otomatis kelas yang sedang diampu langsung di halaman utama berdasarkan waktu saat ini.
* **Validasi Kehadiran Manual:** Fitur *override* status absensi mahasiswa (Hadir, Terlambat, Sakit, Izin, Alpa) dengan aman untuk keperluan toleransi keterlambatan.
* **Ekspor Laporan Cetak Resmi (PDF):** Menghasilkan dokumen rekap absensi berformat PDF resmi universitas lengkap dengan kop, ringkasan statistik persentase, dan kolom tanda tangan siap cetak.

### 🖥️ Modul Layar Kelas (Monitor Absensi Tanpa Login)
* **Smart Background Listener:** Halaman khusus monitor ruangan (`/absen/{ruangan}`) yang secara otomatis aktif mencari jadwal perkuliahan yang sedang berjalan.
* **Real-time RFID Processor:** Menerima data kiriman *tap* kartu mahasiswa dan langsung memproses status kehadiran (Tepat Waktu / Terlambat) berdasarkan parameter toleransi menit yang fleksibel.

---

## 📂 Struktur Folder Utama Proyek
Aplikasi ini mengikuti standar arsitektur modern berkelas tinggi:
```text
absensi-rfid/
├── app/
│   ├── Http/
│   │   ├── Controllers/       # Pusat Logika (Admin, Dosen, Absensi, Dashboard)
│   │   └── Middleware/        # Satpam Proteksi Multi-Role (RoleMiddleware.php)
│   └── Models/                # Struktur Blueprint Database & Relasi Eloquent
├── bootstrap/
│   └── app.php                # Registrasi Alias Middleware & Global Config (Laravel 11)
├── config/                    # Pengaturan Sistem (Timezone: Asia/Jakarta)
├── resources/
│   ├── js/
│   │   ├── Layouts/           # Layout Template Utama (AuthenticatedLayout.vue + SweetAlert2)
│   │   └── Pages/             # Halaman Tampilan Vue (Dashboard, Admin, Dosen)
│   └── views/
│       ├── app.blade.php      # Entry Point HTML (Kustomisasi Favicon Online)
│       └── reports/           # Template Dokumen Cetak (absensi.blade.php untuk DomPDF)
└── routes/
    └── web.php                # Jalur Rute Web dengan Proteksi Ganda Middleware Auth & Role
