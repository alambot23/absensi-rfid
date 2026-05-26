<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import Swal from 'sweetalert2'; // Import SweetAlert ditambahkan

const props = defineProps({
    daftarJadwal: Array,
    daftarDosen: Array,
});

const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    mata_kuliah: '',
    kode_matkul: '',
    sks: 3,
    ruangan: '',
    hari: 'Senin',
    jam_mulai: '',
    jam_selesai: '',
    toleransi_menit: 15,
    dosen_id: '',
    semester: '',
});

// Format jam dari DB (H:i:s) ke input (H:i)
const formatTime = (timeStr) => {
    return timeStr ? timeStr.substring(0, 5) : '';
};

const editJadwal = (jdwl) => {
    isEditing.value = true;
    editingId.value = jdwl.id;
    form.mata_kuliah = jdwl.mata_kuliah;
    form.kode_matkul = jdwl.kode_matkul;
    form.sks = jdwl.sks;
    form.ruangan = jdwl.ruangan;
    form.hari = jdwl.hari;
    form.jam_mulai = formatTime(jdwl.jam_mulai);
    form.jam_selesai = formatTime(jdwl.jam_selesai);
    form.toleransi_menit = jdwl.toleransi_menit;
    form.dosen_id = jdwl.dosen_id;
    form.semester = jdwl.semester;
    form.clearErrors();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEdit = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
};

const submitJadwal = () => {
    if (isEditing.value) {
        form.put(`/admin/jadwal/${editingId.value}`, {
            onSuccess: () => {
                cancelEdit();
                // alert() dihapus, biarkan notifikasi global yang bekerja
            },
        });
    } else {
        form.post('/admin/jadwal', {
            onSuccess: () => {
                form.reset();
                // alert() dihapus, biarkan notifikasi global yang bekerja
            },
        });
    }
};

// Fungsi hapus menggunakan SweetAlert2
const hapusJadwal = (id) => {
    Swal.fire({
        title: 'Hapus Jadwal Kuliah?',
        text: "Yakin ingin menghapus jadwal ini? Seluruh data absensi pada jadwal ini akan ikut terhapus!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#ef4444', 
        cancelButtonColor: '#94a3b8', 
        confirmButtonText: 'Ya, Hapus Permanen!',
        cancelButtonText: 'Batal',
        reverseButtons: true, 
        customClass: {
            popup: 'rounded-2xl shadow-xl border border-slate-100',
            title: 'text-lg font-bold text-slate-800',
            confirmButton: 'rounded-xl font-semibold shadow-sm',
            cancelButton: 'rounded-xl font-semibold'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(`/admin/jadwal/${id}`);
        }
    });
};
</script>

<template>
    <Head title="Manajemen Jadwal" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Jadwal Kuliah</h2>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div class="bg-white border border-gray-200 shadow-sm sm:rounded-xl overflow-hidden transition-all duration-300" :class="{'ring-2 ring-indigo-100': isEditing}">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ isEditing ? 'Edit Jadwal Kuliah' : 'Tambah Jadwal Kuliah Baru' }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">Sistem absensi rfid akan otomatis aktif menyesuaikan ruangan dan jam yang diinput di sini.</p>
                    </div>

                    <div class="p-6">
                        <form @submit.prevent="submitJadwal" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            
                            <div>
                                <InputLabel for="mata_kuliah" value="Nama Mata Kuliah" />
                                <TextInput id="mata_kuliah" type="text" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" v-model="form.mata_kuliah" required />
                                <InputError class="mt-1" :message="form.errors.mata_kuliah" />
                            </div>

                            <div>
                                <InputLabel for="kode_matkul" value="Kode Matkul" />
                                <TextInput id="kode_matkul" type="text" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" v-model="form.kode_matkul" required />
                                <InputError class="mt-1" :message="form.errors.kode_matkul" />
                            </div>

                            <div>
                                <InputLabel for="sks" value="Jumlah SKS (Kredit)" />
                                <TextInput id="sks" type="number" min="1" max="6" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" v-model="form.sks" required />
                                <InputError class="mt-1" :message="form.errors.sks" />
                            </div>

                            <div>
                                <InputLabel for="ruangan" value="Ruangan (Contoh: Ruang 301)" />
                                <TextInput id="ruangan" type="text" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" v-model="form.ruangan" required />
                                <InputError class="mt-1" :message="form.errors.ruangan" />
                            </div>

                            <div>
                                <InputLabel for="dosen_id" value="Dosen Pengajar" />
                                <select id="dosen_id" v-model="form.dosen_id" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="" disabled>-- Pilih Dosen --</option>
                                    <option v-for="dsn in daftarDosen" :key="dsn.id" :value="dsn.id">{{ dsn.name }}</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.dosen_id" />
                            </div>

                            <div>
                                <InputLabel for="hari" value="Hari" />
                                <select id="hari" v-model="form.hari" class="mt-1 block w-full border-gray-300 rounded-lg shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                                    <option value="Senin">Senin</option><option value="Selasa">Selasa</option>
                                    <option value="Rabu">Rabu</option><option value="Kamis">Kamis</option>
                                    <option value="Jumat">Jumat</option><option value="Sabtu">Sabtu</option>
                                </select>
                                <InputError class="mt-1" :message="form.errors.hari" />
                            </div>

                            <div>
                                <InputLabel for="toleransi_menit" value="Toleransi Keterlambatan (Menit)" />
                                <TextInput id="toleransi_menit" type="number" min="0" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" v-model="form.toleransi_menit" required />
                                <InputError class="mt-1" :message="form.errors.toleransi_menit" />
                            </div>

                            <div>
                                <InputLabel for="jam_mulai" value="Jam Mulai" />
                                <TextInput id="jam_mulai" type="time" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" v-model="form.jam_mulai" required />
                                <InputError class="mt-1" :message="form.errors.jam_mulai" />
                            </div>

                            <div>
                                <InputLabel for="jam_selesai" value="Jam Selesai" />
                                <TextInput id="jam_selesai" type="time" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" v-model="form.jam_selesai" required />
                                <InputError class="mt-1" :message="form.errors.jam_selesai" />
                            </div>

                            <div class="md:col-span-2">
                                <InputLabel for="semester" value="Semester / Tahun Akademik" />
                                <TextInput id="semester" type="text" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500" placeholder="Contoh: Genap 2025/2026" v-model="form.semester" required />
                                <InputError class="mt-1" :message="form.errors.semester" />
                            </div>

                            <div class="md:col-span-2 flex items-center gap-4 pt-4 border-t border-gray-100">
                                <PrimaryButton :disabled="form.processing" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-2.5 rounded-lg shadow-sm">
                                    {{ isEditing ? 'Update Jadwal' : 'Simpan Jadwal Kuliah' }}
                                </PrimaryButton>
                                <button v-if="isEditing" type="button" @click="cancelEdit" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors shadow-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 shadow-sm sm:rounded-xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50 flex justify-between items-center">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Jadwal Kuliah Aktif</h3>
                        <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full border border-indigo-200">{{ daftarJadwal?.length || 0 }} Jadwal</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Mata Kuliah & Dosen</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Ruangan</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Waktu</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="jdwl in daftarJadwal" :key="jdwl.id" class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ jdwl.mata_kuliah }}</div>
                                        <div class="text-gray-500 text-xs mt-0.5">{{ jdwl.dosen?.name || 'Dosen Tidak Diketahui' }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700 border border-blue-200">
                                            {{ jdwl.ruangan }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-700">{{ jdwl.hari }}</div>
                                        <div class="text-gray-500 text-xs mt-0.5">{{ formatTime(jdwl.jam_mulai) }} - {{ formatTime(jdwl.jam_selesai) }} WIB</div>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-4">
                                        <button @click="editJadwal(jdwl)" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm transition-colors">Edit</button>
                                        <button @click="hapusJadwal(jdwl.id)" class="text-rose-600 hover:text-rose-900 font-medium text-sm transition-colors">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="daftarJadwal.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="text-gray-400 mb-2">
                                            <svg class="mx-auto h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                        </div>
                                        <p class="text-gray-500">Belum ada jadwal yang didaftarkan.</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>