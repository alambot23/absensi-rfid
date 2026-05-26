<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ daftarJadwal: Array });

// Fungsi untuk memformat jam (misal 15:30:00 menjadi 15:30)
const formatTime = (time) => time ? time.substring(0, 5) : '';
</script>

<template>
    <Head title="Jadwal Mengajar" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">Jadwal & Kelas Saya</h2>
                <span class="px-3 py-1 bg-indigo-50 text-indigo-700 text-xs font-bold rounded-full border border-indigo-100">
                    Semester Aktif
                </span>
            </div>
        </template>

        <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            
            <div class="bg-white border border-slate-200/60 rounded-2xl p-6 shadow-sm flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-bold text-slate-900">Daftar Mata Kuliah</h3>
                    <p class="text-sm text-slate-500 mt-1">Pilih kelas untuk membuka layar *scanning* RFID atau mengunduh laporan.</p>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477-4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
            </div>

            <div v-if="daftarJadwal.length > 0" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="jdwl in daftarJadwal" :key="jdwl.id" class="bg-white border border-slate-200/60 rounded-2xl shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col overflow-hidden group">
                    
                    <div class="p-6 flex-grow border-b border-slate-100 relative">
                        <div class="absolute top-0 right-0 w-24 h-24 bg-gradient-to-br from-indigo-50 to-transparent rounded-bl-full opacity-50 group-hover:scale-110 transition-transform"></div>
                        
                        <span class="inline-block bg-slate-100 text-slate-600 text-xs font-bold px-3 py-1 rounded-md mb-3 tracking-wide">
                            {{ jdwl.kode_matkul }}
                        </span>
                        
                        <h4 class="text-xl font-bold text-slate-800 leading-tight mb-4 group-hover:text-indigo-600 transition-colors">
                            {{ jdwl.mata_kuliah }}
                        </h4>
                        
                        <div class="space-y-3">
                            <div class="flex items-center text-sm text-slate-600">
                                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span class="font-medium">{{ jdwl.hari }}</span>, {{ formatTime(jdwl.jam_mulai) }} - {{ formatTime(jdwl.jam_selesai) }} WIB
                            </div>
                            <div class="flex items-center text-sm text-slate-600">
                                <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                                <span>Ruang: <strong class="text-slate-800">{{ jdwl.ruangan }}</strong></span>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-slate-50 flex flex-col gap-3">
                        <a :href="'/absen/' + jdwl.ruangan_slug" target="_blank" class="w-full flex justify-center items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2.5 rounded-xl text-sm font-semibold transition-all shadow-sm hover:shadow-indigo-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Buka Layar Absensi Kelas
                        </a>

                        <a :href="route('dosen.export', jdwl.id)" class="w-full flex justify-center items-center gap-2 bg-white border border-slate-200 text-slate-700 hover:text-indigo-600 hover:border-indigo-200 hover:bg-indigo-50 px-4 py-2.5 rounded-xl text-sm font-semibold transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Unduh Laporan (PDF)
                        </a>
                    </div>
                </div>
            </div>

            <div v-else class="bg-white border border-slate-200 border-dashed rounded-2xl p-12 text-center">
                <svg class="w-16 h-16 text-slate-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                <h3 class="text-lg font-bold text-slate-800">Belum Ada Jadwal</h3>
                <p class="text-slate-500 mt-1">Anda belum ditugaskan untuk mengajar di kelas manapun semester ini.</p>
            </div>

        </div>
    </AuthenticatedLayout>
</template>