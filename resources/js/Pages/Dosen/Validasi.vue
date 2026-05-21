<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({ dataAbsensi: Array });

// Fungsi update status langsung ke database
const gantiStatus = (id, statusBaru) => {
    const form = useForm({ status: statusBaru });
    form.put(`/dosen/validasi/${id}`, {
        preserveScroll: true,
        onSuccess: () => alert('Status berhasil diperbarui secara manual.')
    });
};

const getStatusColor = (status) => {
    const colors = {
        hadir: 'bg-green-100 text-green-800',
        terlambat: 'bg-yellow-100 text-yellow-800',
        sakit: 'bg-blue-100 text-blue-800',
        izin: 'bg-purple-100 text-purple-800',
        alpa: 'bg-red-100 text-red-800',
    };
    return colors[status] || 'bg-gray-100 text-gray-800';
};
</script>

<template>
    <Head title="Validasi Absensi" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Validasi Absensi Kelas Hari Ini</h2>
        </template>

        <div class="py-10 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white border border-gray-200 shadow-sm sm:rounded-xl overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                    <h3 class="text-lg font-semibold text-gray-900">Aktivitas Tap Kartu Terbaru</h3>
                    <p class="text-sm text-gray-500">Ubah status secara manual jika ada mahasiswa yang menitipkan surat sakit/izin namun tidak tap kartu.</p>
                </div>
                
                <div class="overflow-x-auto">
                    <table class="w-full text-sm text-left">
                        <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Waktu Tap</th>
                                <th class="px-6 py-4 font-semibold">Mahasiswa</th>
                                <th class="px-6 py-4 font-semibold">Mata Kuliah & Ruang</th>
                                <th class="px-6 py-4 font-semibold">Status Saat Ini</th>
                                <th class="px-6 py-4 font-semibold text-right">Ubah Status (Override)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            <tr v-for="absen in dataAbsensi" :key="absen.id" class="hover:bg-gray-50/50">
                                <td class="px-6 py-4 font-mono text-gray-600">{{ new Date(absen.waktu_tap).toLocaleTimeString('id-ID', {hour: '2-digit', minute:'2-digit'}) }}</td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ absen.mahasiswa.name }}</div>
                                    <div class="text-gray-500 text-xs mt-0.5">{{ absen.mahasiswa.nim_nidn }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-gray-900">{{ absen.jadwal.mata_kuliah }}</div>
                                    <div class="text-gray-500 text-xs mt-0.5">{{ absen.jadwal.ruangan }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase" :class="getStatusColor(absen.status)">
                                        {{ absen.status }}
                                    </span>
                                    <span v-if="absen.is_manual_override" class="block mt-1 text-[10px] text-gray-400 font-bold">*Diubah Manual</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <select @change="gantiStatus(absen.id, $event.target.value)" class="text-xs border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded shadow-sm">
                                        <option value="" disabled selected>-- Pilih Tindakan --</option>
                                        <option value="hadir">Jadikan Hadir</option>
                                        <option value="sakit">Sakit (Ada Surat)</option>
                                        <option value="izin">Izin</option>
                                        <option value="alpa">Alpa</option>
                                    </select>
                                </td>
                            </tr>
                            <tr v-if="dataAbsensi.length === 0">
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">Belum ada aktivitas absensi di kelas Anda hari ini.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>