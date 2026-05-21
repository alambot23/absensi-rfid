<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

const props = defineProps({
    daftarDosen: Array
});

// Variabel untuk mendeteksi apakah sedang mode edit
const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    nim_nidn: '',
    email: '',
});

// Fungsi untuk menarik data dari tabel ke form
const editDosen = (dsn) => {
    isEditing.value = true;
    editingId.value = dsn.id;
    form.name = dsn.name;
    form.nim_nidn = dsn.nim_nidn;
    form.email = dsn.email;
    form.clearErrors();
    window.scrollTo({ top: 0, behavior: 'smooth' }); // Otomatis scroll ke atas
};

// Fungsi batal edit
const cancelEdit = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
};

// Fungsi simpan (bisa Tambah Baru atau Update Edit)
const submitDosen = () => {
    if (isEditing.value) {
        form.put(`/admin/dosen/${editingId.value}`, {
            onSuccess: () => {
                cancelEdit();
                alert('Data dosen berhasil diperbarui!');
            },
        });
    } else {
        form.post('/admin/dosen', {
            onSuccess: () => {
                form.reset();
                alert('Dosen berhasil didaftarkan! Password default adalah: password');
            },
        });
    }
};

// Fungsi hapus data dengan konfirmasi
const hapusDosen = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus dosen ini?')) {
        router.delete(`/admin/dosen/${id}`, {
            onSuccess: () => alert('Data dosen berhasil dihapus!')
        });
    }
};
</script>

<template>
    <Head title="Manajemen Dosen" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Data Dosen</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg" :class="{'ring-2 ring-indigo-500': isEditing}">
                    <header>
                        <h2 class="text-lg font-medium text-gray-900">
                            {{ isEditing ? 'Edit Data Dosen' : 'Tambahkan Dosen Baru' }}
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            {{ isEditing ? 'Silakan perbarui data di bawah ini.' : 'Dosen yang didaftarkan akan muncul sebagai pilihan saat membuat Jadwal Kuliah.' }}
                        </p>
                    </header>

                    <form @submit.prevent="submitDosen" class="mt-6 space-y-6 max-w-xl">
                        <div>
                            <InputLabel for="name" value="Nama Lengkap (beserta Gelar)" />
                            <TextInput id="name" type="text" class="mt-1 block w-full" v-model="form.name" required autofocus />
                            <InputError class="mt-2" :message="form.errors.name" />
                        </div>

                        <div>
                            <InputLabel for="nim_nidn" value="NIDN / NIP" />
                            <TextInput id="nim_nidn" type="text" class="mt-1 block w-full" v-model="form.nim_nidn" required />
                            <InputError class="mt-2" :message="form.errors.nim_nidn" />
                        </div>

                        <div>
                            <InputLabel for="email" value="Email Dosen" />
                            <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                            <InputError class="mt-2" :message="form.errors.email" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">
                                {{ isEditing ? 'Update Data' : 'Simpan Data Dosen' }}
                            </PrimaryButton>
                            
                            <button 
                                v-if="isEditing" 
                                type="button" 
                                @click="cancelEdit"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            >
                                Batal
                            </button>
                        </div>
                    </form>
                </div>

                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <h2 class="text-lg font-medium text-gray-900 mb-4">Daftar Dosen Aktif</h2>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-gray-500">
                            <thead class="text-xs text-gray-700 uppercase bg-gray-100">
                                <tr>
                                    <th class="px-4 py-3">Nama Dosen</th>
                                    <th class="px-4 py-3">NIDN / NIP</th>
                                    <th class="px-4 py-3">Email</th>
                                    <th class="px-4 py-3 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="dsn in daftarDosen" :key="dsn.id" class="border-b hover:bg-gray-50">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ dsn.name }}</td>
                                    <td class="px-4 py-3">{{ dsn.nim_nidn }}</td>
                                    <td class="px-4 py-3">{{ dsn.email }}</td>
                                    <td class="px-4 py-3 text-center">
                                        <button @click="editDosen(dsn)" class="text-indigo-600 hover:text-indigo-900 mr-3 font-medium">
                                            Edit
                                        </button>
                                        <button @click="hapusDosen(dsn.id)" class="text-red-600 hover:text-red-900 font-medium">
                                            Hapus
                                        </button>
                                    </td>
                                </tr>
                                <tr v-if="daftarDosen.length === 0">
                                    <td colspan="4" class="px-4 py-4 text-center text-gray-500">Belum ada data dosen.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>