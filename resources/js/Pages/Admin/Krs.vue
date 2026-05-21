<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';
import { ref, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    daftarJadwal: Array,
    daftarMahasiswa: Array
});

// State untuk menyimpan daftar mahasiswa yang saat ini berada di kelas terpilih
const pesertaSekarang = ref([]);
const isLoadingPeserta = ref(false);

const form = useForm({
    jadwal_id: '',
    mahasiswa_ids: [], // Menyimpan ID mahasiswa yang dicentang
});

// Watcher: Jika admin mengubah pilihan Jadwal Kuliah, ambil data peserta kelasnya saat ini
watch(() => form.jadwal_id, async (newJadwalId) => {
    if (!newJadwalId) {
        pesertaSekarang.value = [];
        return;
    }
    
    isLoadingPeserta.value = true;
    form.mahasiswa_ids = []; // Reset pilihan centang sebelumnya
    
    try {
        const response = await axios.get(`/admin/krs/peserta/${newJadwalId}`);
        pesertaSekarang.value = response.data;
    } catch (error) {
        console.error('Gagal memuat peserta kelas:', error);
    } finally {
        isLoadingPeserta.value = false;
    }
});

const submitPlotting = () => {
    if (form.mahasiswa_ids.length === 0) {
        alert('Silakan pilih minimal satu mahasiswa untuk dimasukkan ke kelas.');
        return;
    }

    form.post('/admin/krs', {
        onSuccess: () => {
            // Segarkan data peserta kelas setelah berhasil disimpan
            const currentId = form.jadwal_id;
            form.jadwal_id = '';
            setTimeout(() => { form.jadwal_id = currentId; }, 50);
            alert('Mahasiswa berhasil di-plot ke dalam kelas!');
        }
    });
};
</script>

<template>
    <Head title="Plotting KRS" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Plotting Peserta Kelas (KRS)</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-3 gap-6">
                
                <div class="lg:col-span-2 p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">Masukkan Mahasiswa ke Kelas</h2>
                        <p class="mt-1 text-sm text-gray-600">Pilih kelas target, centang nama mahasiswa, lalu klik simpan.</p>
                    </header>

                    <form @submit.prevent="submitPlotting" class="mt-6 space-y-6">
                        <div>
                            <InputLabel for="jadwal_id" value="Pilih Kelas / Jadwal Kuliah" />
                            <select id="jadwal_id" v-model="form.jadwal_id" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm" required>
                                <option value="" disabled>-- Pilih Jadwal Kuliah --</option>
                                <option v-for="jwl in daftarJadwal" :key="jwl.id" :value="jwl.id">
                                    {{ jwl.mata_kuliah }} ({{ jwl.ruangan }} — {{ jwl.hari }}, {{ jwl.jam_mulai }})
                                </option>
                            </select>
                            <InputError class="mt-2" :message="form.errors.jadwal_id" />
                        </div>

                        <div>
                            <InputLabel value="Pilih Mahasiswa yang Akan Didaftarkan" class="mb-2" />
                            <div class="border border-gray-200 rounded-md max-h-64 overflow-y-auto p-4 space-y-2 bg-gray-50">
                                <div v-for="mhs in daftarMahasiswa" :key="mhs.id" class="flex items-center">
                                    <input 
                                        :id="'mhs-' + mhs.id" 
                                        type="checkbox" 
                                        :value="mhs.id" 
                                        v-model="form.mahasiswa_ids"
                                        class="rounded border-gray-300 text-indigo-600 shadow-sm focus:ring-indigo-500 h-4 w-4"
                                    />
                                    <label :for="'mhs-' + mhs.id" class="ml-3 text-sm text-gray-700">
                                        <span class="font-semibold">{{ mhs.nim_nidn }}</span> — {{ mhs.name }}
                                    </label>
                                </div>
                                <div v-if="daftarMahasiswa.length === 0" class="text-gray-500 text-sm text-center py-4">
                                    Belum ada data mahasiswa di sistem.
                                </div>
                            </div>
                            <InputError class="mt-2" :message="form.errors.mahasiswa_ids" />
                        </div>

                        <div class="pt-2">
                            <PrimaryButton :disabled="form.processing || !form.jadwal_id">
                                Daftarkan Mahasiswa Pilihan
                            </PrimaryButton>
                        </div>
                    </form>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg h-fit">
                    <h3 class="text-md font-bold text-gray-900 mb-2">Anggota Kelas Saat Ini</h3>
                    <p class="text-xs text-gray-500 mb-4">Menampilkan mahasiswa yang sudah resmi terdaftar di kelas terpilih.</p>

                    <div v-if="isLoadingPeserta" class="text-sm text-gray-500 text-center py-6">
                        Memuat daftar peserta kelas...
                    </div>
                    
                    <div v-else-if="form.jadwal_id" class="space-y-3">
                        <div v-for="(peserta, index) in pesertaSekarang" :key="peserta.id" class="flex items-center gap-3 p-2 bg-blue-50 border border-blue-100 rounded-md">
                            <div class="text-xs bg-blue-500 text-white font-bold h-5 w-5 rounded-full flex items-center justify-content-center flex-shrink-0">
                                {{ index + 1 }}
                            </div>
                            <div class="min-w-0">
                                <p class="text-xs font-bold text-gray-800 truncate">{{ peserta.name }}</p>
                                <p class="text-[10px] text-gray-500 font-mono">NIM: {{ peserta.nim_nidn }}</p>
                            </div>
                        </div>
                        <div v-if="pesertaSekarang.length === 0" class="text-sm text-center text-orange-500 bg-orange-50 p-4 rounded border border-orange-200">
                            Kelas ini belum memiliki peserta. Silakan plot mahasiswa di form sebelah kiri.
                        </div>
                    </div>

                    <div v-else class="text-sm text-center text-gray-400 bg-gray-50 p-6 rounded border-2 border-dashed">
                        Silakan pilih kelas di sebelah kiri untuk melihat daftar peserta.
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>