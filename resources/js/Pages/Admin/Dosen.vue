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
                // alert() dihapus, biarkan notifikasi global yang menangani
            },
        });
    } else {
        form.post('/admin/dosen', {
            onSuccess: () => {
                form.reset();
                // alert() dihapus, biarkan notifikasi global yang menangani
            },
        });
    }
};

// Fungsi hapus data dengan SweetAlert2 Premium
const hapusDosen = (id) => {
    Swal.fire({
        title: 'Hapus Data Dosen?',
        text: "Yakin ingin menghapus dosen ini? Akun login dan seluruh data dosen ini akan dihapus permanen!",
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
            router.delete(`/admin/dosen/${id}`);
        }
    });
};
</script>

<template>
    <Head title="Manajemen Dosen" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Data Dosen</h2>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div class="bg-white border border-gray-200 shadow-sm sm:rounded-xl overflow-hidden transition-all duration-300" :class="{'ring-2 ring-indigo-100': isEditing}">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ isEditing ? 'Edit Data Dosen' : 'Tambahkan Dosen Baru' }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ isEditing ? 'Silakan perbarui data profil dosen di bawah ini.' : 'Dosen yang didaftarkan otomatis mendapatkan akses login ke sistem dan akan muncul sebagai pilihan saat membuat Jadwal Kuliah.' }}
                        </p>
                        <p v-if="!isEditing" class="mt-2 text-xs font-semibold text-indigo-600 bg-indigo-50 inline-block px-2.5 py-1 rounded-md border border-indigo-100">
                            Password default dosen baru adalah: password
                        </p>
                    </div>

                    <div class="p-6">
                        <form @submit.prevent="submitDosen" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="md:col-span-2">
                                <InputLabel for="name" value="Nama Lengkap (beserta Gelar)" class="text-gray-700" />
                                <TextInput id="name" type="text" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" v-model="form.name" required autofocus />
                                <InputError class="mt-1" :message="form.errors.name" />
                            </div>

                            <div>
                                <InputLabel for="nim_nidn" value="NIDN / NIP" class="text-gray-700" />
                                <TextInput id="nim_nidn" type="text" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" v-model="form.nim_nidn" required />
                                <InputError class="mt-1" :message="form.errors.nim_nidn" />
                            </div>

                            <div>
                                <InputLabel for="email" value="Email Akses Login Dosen" class="text-gray-700" />
                                <TextInput id="email" type="email" class="mt-1 block w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm" v-model="form.email" required />
                                <InputError class="mt-1" :message="form.errors.email" />
                            </div>

                            <div class="md:col-span-2 flex items-center gap-4 pt-4 border-t border-gray-100">
                                <PrimaryButton :disabled="form.processing" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-2.5 rounded-lg shadow-sm">
                                    {{ isEditing ? 'Update Data Dosen' : 'Simpan Data Dosen' }}
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
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Dosen Aktif</h3>
                        <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full border border-indigo-200">{{ daftarDosen?.length || 0 }} Dosen</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Nama Dosen & Email</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">NIDN / NIP</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="dsn in daftarDosen" :key="dsn.id" class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ dsn.name }}</div>
                                        <div class="text-gray-500 text-xs mt-0.5">{{ dsn.email }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-sm text-gray-600">{{ dsn.nim_nidn }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-4">
                                        <button @click="editDosen(dsn)" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm transition-colors">Edit</button>
                                        <button @click="hapusDosen(dsn.id)" class="text-rose-600 hover:text-rose-900 font-medium text-sm transition-colors">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="daftarDosen.length === 0">
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="text-gray-400 mb-2">
                                            <svg class="mx-auto h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        </div>
                                        <p class="text-gray-500">Belum ada data dosen yang didaftarkan.</p>
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