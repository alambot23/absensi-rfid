<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';

defineProps({ daftarRuangan: Array });

// Fungsi untuk membuat slug URL (mengubah "Ruang 301" menjadi "ruang-301")
const getUrlRuangan = (nama) => {
    const slug = nama.toLowerCase().replace(/ /g, '-');
    return `/absen/${slug}`;
};
</script>

<template>
    <Head title="Manajemen Ruangan" />
    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Daftar Ruangan Aktif</h2>
        </template>
        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div v-for="ruang in daftarRuangan" :key="ruang" class="bg-white p-6 shadow rounded-lg text-center">
                        <h3 class="text-xl font-bold mb-4">{{ ruang }}</h3>
                        <a :href="getUrlRuangan(ruang)" target="_blank" class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700">
                            Buka Layar Monitor
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>