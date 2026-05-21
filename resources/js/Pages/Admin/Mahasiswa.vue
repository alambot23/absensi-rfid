<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import InputError from '@/Components/InputError.vue';

// PERBAIKAN: Namanya disamakan dengan yang dikirim Controller (mahasiswa)
const props = defineProps({
    mahasiswa: Array 
});

const isEditing = ref(false);
const editingId = ref(null);

const form = useForm({
    name: '',
    nim_nidn: '',
    email: '',
    rfid_uid: '',
});

const editMahasiswa = (mhs) => {
    isEditing.value = true;
    editingId.value = mhs.id;
    form.name = mhs.name;
    form.nim_nidn = mhs.nim_nidn;
    form.email = mhs.email;
    form.rfid_uid = mhs.rfid_uid;
    form.clearErrors();
    window.scrollTo({ top: 0, behavior: 'smooth' });
};

const cancelEdit = () => {
    isEditing.value = false;
    editingId.value = null;
    form.reset();
    form.clearErrors();
};

const submitMahasiswa = () => {
    if (isEditing.value) {
        form.put(`/admin/mahasiswa/${editingId.value}`, {
            onSuccess: () => {
                cancelEdit();
                alert('Data Mahasiswa berhasil diperbarui!');
            },
        });
    } else {
        form.post('/admin/mahasiswa', {
            onSuccess: () => {
                form.reset();
                alert('Mahasiswa berhasil didaftarkan!');
            },
        });
    }
};

const hapusMahasiswa = (id) => {
    if (confirm('Yakin ingin menghapus data mahasiswa ini?')) {
        router.delete(`/admin/mahasiswa/${id}`);
    }
};
</script>

<template>
    <Head title="Manajemen Mahasiswa" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Manajemen Mahasiswa & RFID</h2>
        </template>

        <div class="py-10">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
                
                <div class="bg-white border border-gray-200 shadow-sm sm:rounded-xl overflow-hidden transition-all duration-300" :class="{'ring-2 ring-indigo-100': isEditing}">
                    <div class="px-6 py-5 border-b border-gray-100 bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-900">
                            {{ isEditing ? 'Edit Data Mahasiswa' : 'Pendaftaran Kartu RFID Baru' }}
                        </h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ isEditing ? 'Perbarui informasi mahasiswa atau ganti UID kartunya.' : 'Isi biodata dan tap kartu ke alat *reader* untuk mendaftarkan mahasiswa.' }}
                        </p>
                    </div>

                    <div class="p-6">
                        <form @submit.prevent="submitMahasiswa" class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <InputLabel for="name" value="Nama Lengkap" class="text-gray-700" />
                                    <TextInput id="name" type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" v-model="form.name" required />
                                    <InputError class="mt-1" :message="form.errors.name" />
                                </div>
                                <div>
                                    <InputLabel for="nim_nidn" value="Nomor Induk Mahasiswa (NIM)" class="text-gray-700" />
                                    <TextInput id="nim_nidn" type="text" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" v-model="form.nim_nidn" required />
                                    <InputError class="mt-1" :message="form.errors.nim_nidn" />
                                </div>
                                <div>
                                    <InputLabel for="email" value="Email" class="text-gray-700" />
                                    <TextInput id="email" type="email" class="mt-1 block w-full border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-sm" v-model="form.email" required />
                                    <InputError class="mt-1" :message="form.errors.email" />
                                </div>
                            </div>

                            <div class="space-y-4 flex flex-col justify-center">
                                <div class="bg-indigo-50 p-6 rounded-xl border border-indigo-100 relative overflow-hidden">
                                    <div class="absolute top-0 right-0 p-4 opacity-10">
                                        <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8zm0-14c-3.31 0-6 2.69-6 6s2.69 6 6 6 6-2.69 6-6-2.69-6-6-6zm0 10c-2.21 0-4-1.79-4-4s1.79-4 4-4 4 1.79 4 4-1.79 4-4 4z"/></svg>
                                    </div>
                                    <InputLabel for="rfid_uid" value="Tap Kartu RFID Sekarang" class="text-indigo-900 font-bold mb-2" />
                                    <TextInput 
                                        id="rfid_uid" 
                                        type="text" 
                                        class="mt-1 block w-full font-mono text-center text-lg tracking-widest border-indigo-300 focus:border-indigo-600 focus:ring-indigo-600 rounded-lg shadow-inner bg-white" 
                                        v-model="form.rfid_uid" 
                                        placeholder="Scan kartu disini..." 
                                        required 
                                        autofocus 
                                    />
                                    <p class="mt-2 text-xs text-indigo-600 font-medium">Pastikan kursor berkedip di dalam kotak ini saat menempelkan kartu.</p>
                                    <InputError class="mt-1" :message="form.errors.rfid_uid" />
                                </div>
                            </div>

                            <div class="col-span-1 md:col-span-2 flex items-center gap-4 pt-4 border-t border-gray-100">
                                <PrimaryButton :disabled="form.processing" class="bg-gray-900 hover:bg-gray-800 text-white px-6 py-2.5 rounded-lg shadow-sm">
                                    {{ isEditing ? 'Update Data Mahasiswa' : 'Simpan Data Mahasiswa' }}
                                </PrimaryButton>
                                <button v-if="isEditing" type="button" @click="cancelEdit" class="px-6 py-2.5 bg-white border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 font-medium text-sm transition-colors shadow-sm">
                                    Batal
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 shadow-sm sm:rounded-xl overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="text-lg font-semibold text-gray-900">Daftar Mahasiswa Terdaftar</h3>
                        <span class="bg-indigo-100 text-indigo-700 text-xs font-bold px-3 py-1 rounded-full border border-indigo-200">{{ mahasiswa?.length || 0 }} Data</span>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-gray-500 uppercase bg-gray-50 border-b border-gray-200">
                                <tr>
                                    <th class="px-6 py-4 font-semibold tracking-wider">Nama & Email</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">NIM</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider">UID Kartu</th>
                                    <th class="px-6 py-4 font-semibold tracking-wider text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                <tr v-for="mhs in mahasiswa" :key="mhs.id" class="hover:bg-gray-50/80 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="font-medium text-gray-900">{{ mhs.name }}</div>
                                        <div class="text-gray-500 text-xs mt-0.5">{{ mhs.email }}</div>
                                    </td>
                                    <td class="px-6 py-4 text-gray-600">{{ mhs.nim_nidn }}</td>
                                    <td class="px-6 py-4">
                                        <span class="font-mono text-xs text-indigo-600 bg-indigo-50 px-2.5 py-1 rounded-md border border-indigo-100">{{ mhs.rfid_uid }}</span>
                                    </td>
                                    <td class="px-6 py-4 text-right space-x-4">
                                        <button @click="editMahasiswa(mhs)" class="text-indigo-600 hover:text-indigo-900 font-medium text-sm transition-colors">Edit</button>
                                        <button @click="hapusMahasiswa(mhs.id)" class="text-rose-600 hover:text-rose-900 font-medium text-sm transition-colors">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="!mahasiswa || mahasiswa.length === 0">
                                    <td colspan="4" class="px-6 py-12 text-center">
                                        <div class="text-gray-400 mb-2">
                                            <svg class="mx-auto h-10 w-10" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                                        </div>
                                        <p class="text-gray-500">Belum ada mahasiswa yang didaftarkan.</p>
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