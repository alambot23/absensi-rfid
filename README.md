<div align="center">

  <img src="[https://cdn-icons-png.flaticon.com/512/8654/8654302.png](https://cdn-icons-png.flaticon.com/512/8654/8654302.png)" alt="NexusApp Logo" width="120" height="120" />

  # 🚀 NexusApp — Sistem Absensi RFID & Akademik Terintegrasi

  **"Sistem Presensi & Manajemen Akademik Enterprise dengan Estetika Visual Premium bertema Catppuccin Macchiato."**

  ---

  <img src="[https://img.shields.io/badge/Laravel-v11.x-c6a0f6?style=for-the-badge&logo=laravel&logoColor=24273a](https://img.shields.io/badge/Laravel-v11.x-c6a0f6?style=for-the-badge&logo=laravel&logoColor=24273a)" alt="Laravel" />
  <img src="[https://img.shields.io/badge/Vue.js-v3.x-8bd5ca?style=for-the-badge&logo=vue.js&logoColor=24273a](https://img.shields.io/badge/Vue.js-v3.x-8bd5ca?style=for-the-badge&logo=vue.js&logoColor=24273a)" alt="Vue" />
  <img src="[https://img.shields.io/badge/Inertia.js-v1.x-f5bde6?style=for-the-badge&logo=inertia&logoColor=24273a](https://img.shields.io/badge/Inertia.js-v1.x-f5bde6?style=for-the-badge&logo=inertia&logoColor=24273a)" alt="Inertia" />
  <img src="[https://img.shields.io/badge/Tailwind_CSS-v3.x-7dc4e4?style=for-the-badge&logo=tailwind-css&logoColor=24273a](https://img.shields.io/badge/Tailwind_CSS-v3.x-7dc4e4?style=for-the-badge&logo=tailwind-css&logoColor=24273a)" alt="Tailwind" />
  <img src="[https://img.shields.io/badge/PHP-v8.2-eed49f?style=for-the-badge&logo=php&logoColor=24273a](https://img.shields.io/badge/PHP-v8.2-eed49f?style=for-the-badge&logo=php&logoColor=24273a)" alt="PHP" />

</div>

---

## 📌 Tentang Proyek
**NexusApp** adalah platform sistem informasi manajemen absensi perkuliahan berbasis **Kartu RFID (Radio Frequency Identification)** yang dirancang untuk mendigitalkan proses pencatatan kehadiran di ruang kelas secara *real-time*. Menggunakan arsitektur modern perpaduan **Laravel 11**, **Inertia.js**, dan **Vue 3 (Vite)**, aplikasi ini menawarkan performa super cepat berbasis *Single Page Application* (SPA) dengan desain visual dashboard yang memukau, bersih, dan memanjakan mata.

---

## ✨ Fitur Unggulan (UI Gacor)

### 👑 Modul Administrator (Pusat Komando)
* **Dashboard Statistik Dinamis:** Dilengkapi visualisasi grafik garis tren kehadiran mahasiswa berbasis **Chart.js** dengan gradasi warna premium.
* **Manajemen Data Master Terpusat:** Sistem CRUD mutakhir untuk mengelola data Mahasiswa, Dosen, Ruangan, dan Mata Kuliah beserta SKS dinamis.
* **Plotting KRS Fleksibel:** Kemudahan dalam memasukkan dan memetakan mahasiswa ke dalam kelas/jadwal tertentu secara massal.

### 👨‍🏫 Modul Dosen Pengampu
* **Akses Cepat Pengajaran:** Deteksi otomatis kelas yang sedang diampu langsung di halaman utama berdasarkan waktu saat ini.
* **Validasi Kehadiran Manual:** Fitur *override* status absensi mahasiswa (Hadir, Terlambat, Sakit, Izin, Alpa) dengan aman untuk keperluan toleransi keterlambatan dengan modal konfirmasi **SweetAlert2**.
* **Ekspor Laporan Cetak Resmi (PDF):** Menghasilkan dokumen rekap absensi berformat PDF resmi universitas lengkap dengan kop, ringkasan statistik persentase, dan kolom tanda tangan siap cetak via **DomPDF**.

### 🖥️ Modul Layar Kelas (Monitor Absensi Tanpa Login)
* **Smart Background Listener:** Halaman khusus monitor ruangan (`/absen/{ruangan}`) yang secara otomatis aktif mencari jadwal perkuliahan yang sedang berjalan.
* **Real-time RFID Processor:** Menerima data kiriman *tap* kartu mahasiswa dan langsung memproses status kehadiran (Tepat Waktu / Terlambat) berdasarkan parameter toleransi menit yang fleksibel.

---

## 🛠️ Panduan Langkah Instalasi

Ikuti urutan perintah berikut pada terminal terminal komputer Anda untuk memasang proyek dari awal:

### 1. Persiapan Berkas & Dependensi Backend
```bash
# Kloning proyek dari repositori
git clone https://github.com/USERNAME-ANDA/absensi-rfid.git
cd absensi-rfid

# Pasang paket dependensi PHP pihak ketiga
composer install

# Salin berkas konfigurasi environment aplikasi
cp .env.example .env
```
> **PENTING:** Buka file `.env` yang baru dibuat, lalu sesuaikan nama database Anda pada baris `DB_DATABASE=absen_rfid` beserta `DB_USERNAME` dan `DB_PASSWORD` milik MySQL lokal Anda.

### 2. Konfigurasi Database & Kunci Keamanan
```bash
# Membuat kunci enkripsi unik untuk keamanan aplikasi
php artisan key:generate

# Jalankan skrip migrasi tabel beserta pengisian data awal (seeder)
php artisan migrate --seed
```

### 3. Persiapan Aset Frontend
```bash
# Pasang paket dependensi Node.js untuk Vue & Tailwind
npm install

# Jalankan server kompilasi aset Vite (Biarkan terminal ini tetap terbuka)
npm run dev
```

### 4. Menjalankan Server Aplikasi
Buka jendela atau tab terminal baru di folder yang sama, lalu jalankan perintah:
```bash
php artisan serve
```
Aplikasi Anda sekarang siap diakses melalui tautan web browser di **`http://localhost:8000`** 🎯

---

## 📋 SOP Penggunaan Sistem (Standar Operasional)

### 📈 Alur Kerja 1: Persiapan oleh Administrator
1. Masuk ke akun Admin, masuk ke menu **Manajemen Dosen & Mahasiswa** untuk mendaftarkan akun pengguna baru.
2. Tambahkan data ruangan fisik kelas serta mata kuliah beserta jumlah kredit SKS yang valid.
3. Masuk ke menu **Jadwal Kuliah**, buat entri jadwal baru dengan menetapkan dosen pengampu, jam mulai/selesai, serta durasi toleransi keterlambatan dalam menit.
4. Gunakan fitur **Plotting KRS** untuk memasukkan daftar mahasiswa ke dalam kelas jadwal tersebut.

### 🎛️ Alur Kerja 2: Operasional Layar Kelas (RFID Tap)
1. Buka browser pada monitor komputer ruang kelas tanpa perlu melakukan login.
2. Arahkan URL ke rute khusus ruangan terkait, contoh: `http://localhost:8000/absen/ruang1`.
3. Ketika jam kuliah dimulai, mahasiswa melakukan pemindaian fisik kartu RFID pada alat pembaca.
4. Sistem membaca *waktu tap* secara instan dan menentukan status kehadiran secara otomatis (Hadir/Terlambat) berdasarkan batas toleransi waktu.

### 📝 Alur Kerja 3: Validasi & Rekapitulasi oleh Dosen
1. Dosen melakukan login ke sistem menggunakan akun masing-masing.
2. Pada menu **Validasi Absensi**, dosen dapat meninjau rekaman data kehadiran mahasiswa secara *real-time* pada hari berjalan.
3. Jika terdapat kekeliruan atau dispensasi resmi, dosen mengeklik tombol **Edit** untuk mengubah status kehadiran secara manual menggunakan *override input*.
4. Di akhir semester, dosen mengeklik tombol **Unduh Laporan (PDF)** pada baris kelas bersangkutan untuk mencetak dokumen fisik presensi resmi universitas.

---

## 📂 Struktur Folder Utama Proyek

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
│   │   └── Pages/             # Halaman Tampilan Vue (Dashboard, Admin, Dosen, Jadwal)
│   └── views/
│       ├── app.blade.php      # Entry Point HTML Utama (Kustomisasi Favicon Online)
│       └── reports/           # Template Dokumen Cetak (absensi.blade.php untuk DomPDF)
└── routes/
    └── web.php                # Jalur Rute Web dengan Proteksi Ganda Middleware Auth & Role
```

---

<div align="center">
  <p>Dibuat dengan kesempurnaan arsitektur kode oleh Yang Mulia Yang Dipertuankan Agung Maharaja Alam.</p>
  <strong>© 2026 NexusApp — All Rights Reserved.</strong>
</div>
