<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canLogin: { type: Boolean },
    canRegister: { type: Boolean },
});
</script>

<template>
    <Head title="Selamat Datang" />

    <div class="min-h-screen bg-slate-50 text-slate-900 font-sans selection:bg-indigo-500 selection:text-white">
        
        <nav class="container mx-auto px-6 py-5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <ApplicationLogo class="w-8 h-8" />
                <span class="font-bold text-xl tracking-tight text-slate-800">
                    Nexus<span class="text-indigo-600">Attendance</span>
                </span>
            </div>
            
            <div v-if="canLogin" class="space-x-2 sm:space-x-4">
                <Link 
                    v-if="$page.props.auth.user" 
                    :href="route('dashboard')" 
                    class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors"
                >
                    Dashboard
                </Link>
                <template v-else>
                    <Link 
                        :href="route('login')" 
                        class="text-sm font-semibold text-slate-600 hover:text-indigo-600 transition-colors px-3 py-2"
                    >
                        Log in
                    </Link>
                   
                </template>
            </div>
        </nav>

        <main class="container mx-auto px-6 pt-20 pb-24 sm:pt-32 lg:pb-32 text-center">
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 text-xs font-semibold mb-8 border border-indigo-100">
                <span class="flex h-2 w-2 rounded-full bg-indigo-600"></span>
                Sistem Terintegrasi v1.0
            </div>
            
            <h1 class="mx-auto max-w-4xl font-extrabold tracking-tight text-slate-900 sm:text-5xl lg:text-6xl leading-tight">
                Sistem Presensi Kelas Pintar dengan <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">Teknologi RFID</span>.
            </h1>
            
            <p class="mx-auto mt-6 max-w-2xl text-lg text-slate-600 leading-relaxed">
                Otomatisasi pencatatan kehadiran secara real-time. Lebih cepat, akurat, dan dirancang khusus untuk mengoptimalkan manajemen data Universitas Malikussaleh.
            </p>
            
            <div class="mt-10 flex flex-col sm:flex-row justify-center gap-4">
                <Link 
                    v-if="!$page.props.auth.user" 
                    :href="route('login')" 
                    class="bg-gray-900 text-white px-8 py-3.5 rounded-xl font-semibold text-sm hover:bg-gray-800 hover:shadow-lg transition-all flex items-center justify-center gap-2"
                >
                    Masuk ke Sistem
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </Link>
                <Link 
                    v-else 
                    :href="route('dashboard')" 
                    class="bg-indigo-600 text-white px-8 py-3.5 rounded-xl font-semibold text-sm hover:bg-indigo-700 hover:shadow-lg transition-all flex items-center justify-center gap-2"
                >
                    Buka Dashboard
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </Link>
            </div>
        </main>

        <section class="container mx-auto px-6 py-16 border-t border-slate-200/60 bg-white sm:rounded-t-3xl shadow-[0_-10px_40px_rgba(0,0,0,0.02)]">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-10 lg:gap-16">
                <div class="text-center md:text-left">
                    <div class="w-12 h-12 mx-auto md:mx-0 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-5 border border-indigo-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Tap & Go</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Cukup tempelkan kartu RFID pada alat *reader* di ruangan. Kehadiran langsung tercatat ke dalam database tanpa proses manual.</p>
                </div>
                
                <div class="text-center md:text-left">
                    <div class="w-12 h-12 mx-auto md:mx-0 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-5 border border-blue-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Real-Time Monitor</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Setiap ruangan memiliki layar pintar yang langsung memverifikasi nama mahasiswa, status absensi, dan keterlambatan.</p>
                </div>

                <div class="text-center md:text-left">
                    <div class="w-12 h-12 mx-auto md:mx-0 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-5 border border-emerald-100">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">Laporan Otomatis</h3>
                    <p class="text-slate-600 text-sm leading-relaxed">Dosen dan Admin dapat mengunduh rekap kehadiran seluruh mahasiswa dengan satu klik. Bebas repot merekap kertas absensi.</p>
                </div>
            </div>
        </section>

        <footer class="bg-white py-8 text-center text-slate-500 text-sm border-t border-slate-100">
            &copy; {{ new Date().getFullYear() }} NexusAttendance.
        </footer>
    </div>
</template>