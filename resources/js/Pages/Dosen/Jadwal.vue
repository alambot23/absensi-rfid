<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ daftarJadwal: Array });

const formatTime = (time) => time ? time.substring(0, 5) : '';
</script>

<template>
    <Head title="Jadwal Mengajar" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jadwal Mengajar & Laporan</h2>
        </template>

        <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white border border-gray-200 rounded-xl p-6 shadow-sm">
                <h3 class="text-lg font-bold text-gray-900">Mata Kuliah Anda</h3>
                <p class="text-sm text-gray-500">Berikut adalah daftar kelas yang Anda ampu semester ini.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div v-for="jdwl in daftarJadwal" :key="jdwl.id" class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-md transition">
                    <div class="p-6 border-b border-gray-100">
                        <span class="bg-blue-50 text-blue-700 text-xs font-bold px-3 py-1 rounded-full">{{ jdwl.kode_matkul }}</span>
                        <h4 class="text-xl font-bold text-gray-900 mt-3">{{ jdwl.mata_kuliah }}</h4>
                        <div class="space-y-2 mt-4 text-sm text-gray-600">
                            <p>{{ jdwl.hari }}, {{ formatTime(jdwl.jam_mulai) }} - {{ formatTime(jdwl.jam_selesai) }}</p>
                            <p>{{ jdwl.ruangan }}</p>
                        </div>
                    </div>
                    <div class="p-4 bg-gray-50">
                        <a :href="route('dosen.export', jdwl.id)" class="w-full flex justify-center items-center gap-2 bg-white border border-gray-300 text-gray-700 hover:text-indigo-600 px-4 py-2 rounded-lg text-sm font-semibold transition-colors shadow-sm">
                            Unduh Laporan Absensi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>