<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import { ref, watch } from 'vue';

const props = defineProps({
    daftarJadwal: Array,
    laporan: Array,
    filters: Object,
});

// State untuk filter pencarian
const filterJadwal = ref(props.filters.jadwal_id || '');
const filterTanggal = ref(props.filters.tanggal || '');

// Watcher: Reload data otomatis lewat Inertia jika filter diubah
watch([filterJadwal, filterTanggal], ([newJadwal, newTanggal]) => {
    if (newJadwal) {
        router.get('/admin/laporan', {
            jadwal_id: newJadwal,
            tanggal: newTanggal
        }, {
            preserveState: true,
            replace: true,
            preserveScroll: true
        });
    }
});
</script>

<template>
    <Head title="Laporan Absensi" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Rekapitulasi Kehadiran</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="p-4 sm:p-6 bg-white shadow sm:rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="md:col-span-2">
                            <InputLabel for="jadwal" value="Pilih Jadwal / Kelas" />
                            <select id="jadwal" v-model="filterJadwal" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                                <option value="" disabled>-- Pilih Kelas --</option>
                                <option v-for="jwl in daftarJadwal" :key="jwl.id" :value="jwl.id">
                                    {{ jwl.mata_kuliah }} ({{ jwl.ruangan }}) - {{ jwl.dosen.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <InputLabel for="tanggal" value="Tanggal Kuliah" />
                            <TextInput id="tanggal" type="date" class="mt-1 block w-full" v-model="filterTanggal" />
                        </div>
                    </div>
                </div>

                <div v-if="filterJadwal" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h3 class="text-lg font-bold text-gray-900 mb-4">Detail Kehadiran Tanggal: {{ filterTanggal }}</h3>
                    
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500 border border-gray-200">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3">No</th>
                                    <th class="px-4 py-3">Nama Mahasiswa</th>
                                    <th class="px-4 py-3">NIM</th>
                                    <th class="px-4 py-3 text-center">Status</th>
                                    <th class="px-4 py-3 text-center">Waktu Tap</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(row, index) in laporan" :key="row.id" class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3">{{ index + 1 }}</td>
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ row.name }}</td>
                                    <td class="px-4 py-3">{{ row.nim_nidn }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <span v-if="row.status === 'hadir'" class="bg-green-100 text-green-800 text-xs font-bold px-2.5 py-1 rounded">HADIR</span>
                                        <span v-else-if="row.status === 'terlambat'" class="bg-yellow-100 text-yellow-800 text-xs font-bold px-2.5 py-1 rounded">TERLAMBAT</span>
                                        <span v-else class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-1 rounded">ALPA</span>
                                    </td>
                                    <td class="px-4 py-3 text-center font-mono text-gray-600">
                                        {{ row.waktu_tap }}
                                    </td>
                                </tr>
                                <tr v-if="laporan.length === 0">
                                    <td colspan="5" class="px-4 py-8 text-center text-gray-400">
                                        Belum ada data mahasiswa terdaftar (KRS) di kelas ini.
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div v-else class="text-center p-8 bg-gray-50 border-2 border-dashed border-gray-300 rounded-lg text-gray-500">
                    Silakan pilih kelas pada filter di atas untuk melihat data laporan kehadiran.
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>