<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, usePage, Link } from '@inertiajs/vue3';
import { ref, onMounted, onBeforeUnmount } from 'vue';
import Chart from 'chart.js/auto';

// Ambil data user
const user = usePage().props.auth.user;

// --- LOGIKA UNTUK GRAFIK CHART.JS ---
const chartCanvas = ref(null);
let chartInstance = null;

onMounted(() => {
    // Grafik hanya dirender jika elemen canvas ditemukan (misal: di dashboard Admin)
    if (chartCanvas.value) {
        const ctx = chartCanvas.value.getContext('2d');

        // Membuat efek gradasi warna di bawah garis grafik
        const gradient = ctx.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(79, 70, 229, 0.4)'); // Indigo-600 semi transparan
        gradient.addColorStop(1, 'rgba(79, 70, 229, 0.0)'); // Transparan di bawah

        chartInstance = new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'],
                datasets: [{
                    label: 'Kehadiran (%)',
                    data: [85, 92, 78, 98, 88, 95], // Data simulasi
                    borderColor: '#4f46e5', // Warna garis Indigo
                    backgroundColor: gradient,
                    borderWidth: 3,
                    pointBackgroundColor: '#ffffff',
                    pointBorderColor: '#4f46e5',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
                    fill: true,
                    tension: 0.4 // Membuat garis melengkung halus (smooth)
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false }, // Sembunyikan legenda agar bersih
                    tooltip: {
                        backgroundColor: '#1e293b',
                        padding: 12,
                        cornerRadius: 8,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return context.parsed.y + '% Hadir';
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100,
                        border: { display: false },
                        grid: { color: '#f1f5f9' }, // Garis horizontal tipis
                        ticks: { color: '#64748b', stepSize: 25, font: { size: 11 } }
                    },
                    x: {
                        border: { display: false },
                        grid: { display: false }, // Hilangkan garis vertikal
                        ticks: { color: '#64748b', font: { size: 11 } }
                    }
                }
            }
        });
    }
});

// Hapus grafik saat pindah halaman agar memori tidak bocor
onBeforeUnmount(() => {
    if (chartInstance) chartInstance.destroy();
});
</script>

<template>
    <Head title="Dashboard Pusat Komando" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-bold text-2xl text-slate-800 tracking-tight">Overview</h2>
                    <p class="text-sm text-slate-500 mt-1">Sistem Manajemen Presensi & Akademik Terintegrasi.</p>
                </div>
                <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 bg-emerald-50 border border-emerald-100 rounded-full">
                    <span class="flex h-2.5 w-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span class="text-xs font-semibold text-emerald-700 uppercase tracking-wider">Sistem Online</span>
                </div>
            </div>
        </template>

        <div class="py-8 bg-slate-50/50 min-h-[calc(100vh-130px)]">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div v-if="user.role === 'admin'" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-indigo-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Total Mahasiswa</p>
                                <h3 class="text-3xl font-extrabold text-slate-800">1,248</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-indigo-100 text-indigo-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                        <div class="flex items-center text-xs font-medium text-emerald-600 relative z-10">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                            <span>+12 Terdaftar Minggu Ini</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-blue-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Total Dosen Aktif</p>
                                <h3 class="text-3xl font-extrabold text-slate-800">45</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                        <div class="flex items-center text-xs font-medium text-slate-400 relative z-10">
                            <span>Berdasarkan data semester genap</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-amber-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Kelas Hari Ini</p>
                                <h3 class="text-3xl font-extrabold text-slate-800">18</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                        <div class="flex items-center text-xs font-medium text-amber-600 relative z-10">
                            <span>4 Kelas sedang berlangsung</span>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm hover:shadow-md transition-shadow relative overflow-hidden group">
                        <div class="absolute -right-4 -top-4 w-24 h-24 bg-rose-50 rounded-full opacity-50 group-hover:scale-150 transition-transform duration-500"></div>
                        <div class="flex justify-between items-start mb-4 relative z-10">
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Alat Reader Rusak</p>
                                <h3 class="text-3xl font-extrabold text-slate-800">0</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </div>
                        <div class="flex items-center text-xs font-medium text-emerald-600 relative z-10">
                            <span>Semua ruangan normal</span>
                        </div>
                    </div>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <div class="bg-gradient-to-br from-indigo-600 to-blue-700 p-6 rounded-2xl shadow-lg relative overflow-hidden text-white">
                        <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-white opacity-10 rounded-full blur-2xl"></div>
                        <h3 class="text-lg font-semibold text-indigo-100 mb-1">Selamat Datang,</h3>
                        <h2 class="text-2xl font-bold mb-6">{{ user.name }}</h2>
                        <div class="flex justify-between items-end">
                            <div>
                                <p class="text-sm text-indigo-200">Total Kelas Anda</p>
                                <p class="text-3xl font-black">4</p>
                            </div>
                            <svg class="w-12 h-12 text-indigo-300 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        </div>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Jadwal Mengajar Hari Ini</p>
                                <h3 class="text-3xl font-extrabold text-slate-800">1</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-amber-100 text-amber-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <Link :href="route('dosen.jadwal')" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center group">
                            Lihat Rincian Jadwal
                            <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </Link>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-slate-200/60 shadow-sm">
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <p class="text-sm font-medium text-slate-500 mb-1">Perlu Divalidasi</p>
                                <h3 class="text-3xl font-extrabold text-rose-600">3</h3>
                            </div>
                            <div class="w-10 h-10 rounded-xl bg-rose-100 text-rose-600 flex items-center justify-center">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <Link :href="route('dosen.validasi')" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 flex items-center group">
                            Validasi Absensi Sekarang
                            <svg class="w-4 h-4 ml-1 transform group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </Link>
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 pt-4">
                    
                    <div class="lg:col-span-2 bg-white rounded-2xl border border-slate-200/60 shadow-sm p-8">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-slate-100 flex items-center justify-center text-slate-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold text-slate-800">Tren Kehadiran Mahasiswa</h3>
                                    <p class="text-sm text-slate-500">Persentase kehadiran dalam seminggu terakhir.</p>
                                </div>
                            </div>
                        </div>
                        
                        <div class="rounded-xl h-64 w-full relative">
                            <canvas ref="chartCanvas"></canvas>
                        </div>
                    </div>

                    <div class="bg-white rounded-2xl border border-slate-200/60 shadow-sm p-6">
                        <h3 class="text-lg font-bold text-slate-800 mb-6 flex items-center gap-2">
                            <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                            Jalan Pintas
                        </h3>

                        <div v-if="user.role === 'admin'" class="space-y-3">
                            <Link :href="route('admin.mahasiswa')" class="group flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50 hover:bg-indigo-50 hover:border-indigo-100 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    </div>
                                    <span class="font-semibold text-sm text-slate-700 group-hover:text-indigo-900">Daftar RFID Baru</span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-indigo-600 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </Link>

                            <Link :href="route('admin.jadwal')" class="group flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50 hover:bg-blue-50 hover:border-blue-100 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-blue-100 text-blue-600 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                    <span class="font-semibold text-sm text-slate-700 group-hover:text-blue-900">Susun Jadwal Kuliah</span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-blue-600 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </Link>

                            <Link :href="route('admin.krs')" class="group flex items-center justify-between p-4 rounded-xl border border-slate-100 bg-slate-50 hover:bg-emerald-50 hover:border-emerald-100 transition-all">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-600 group-hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                    </div>
                                    <span class="font-semibold text-sm text-slate-700 group-hover:text-emerald-900">Plotting Anggota Kelas</span>
                                </div>
                                <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-600 transform group-hover:translate-x-1 transition-all" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </Link>
                        </div>

                        <div v-else class="space-y-3">
                            <Link :href="route('dosen.jadwal')" class="group flex flex-col justify-center p-5 rounded-xl border border-slate-100 bg-gradient-to-br from-indigo-50 to-white hover:border-indigo-200 transition-all cursor-pointer">
                                <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                </div>
                                <span class="font-bold text-slate-800 text-base mb-1">Cetak Laporan (PDF)</span>
                                <span class="text-xs text-slate-500">Unduh rekap kehadiran mahasiswa per mata kuliah.</span>
                            </Link>

                            <Link :href="route('dosen.validasi')" class="group flex flex-col justify-center p-5 rounded-xl border border-slate-100 bg-gradient-to-br from-blue-50 to-white hover:border-blue-200 transition-all cursor-pointer">
                                <div class="w-10 h-10 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center mb-3">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                                </div>
                                <span class="font-bold text-slate-800 text-base mb-1">Validasi Surat Izin</span>
                                <span class="text-xs text-slate-500">Ubah status absen mahasiswa yang sakit/izin.</span>
                            </Link>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>