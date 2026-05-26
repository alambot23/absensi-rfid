<script setup>
import { ref, watch, onMounted } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import NavLink from '@/Components/NavLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';
import Swal from 'sweetalert2';

const showingNavigationDropdown = ref(false);
const user = usePage().props.auth.user;
const page = usePage();

// Watcher untuk mendeteksi pesan 'success' atau 'error' dari Controller Laravel
const showNotification = () => {
    if (page.props.flash && page.props.flash.success) {
        Swal.fire({
            icon: 'success',
            title: 'Berhasil!',
            text: page.props.flash.success,
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            background: '#ffffff',
            color: '#1e293b',
            iconColor: '#10b981',
            customClass: { popup: 'rounded-xl shadow-lg border border-slate-100' }
        });
    }
};

onMounted(showNotification);
watch(() => page.props.flash, showNotification, { deep: true });
</script>

<template>
    <div>
        <div class="min-h-screen bg-slate-50 text-slate-900 font-sans selection:bg-indigo-500 selection:text-white">
            
            <nav class="sticky top-0 z-50 bg-white/80 backdrop-blur-md border-b border-gray-200 shadow-sm">
                <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                    <div class="flex h-16 justify-between items-center">
                        <div class="flex items-center">
                            <div class="flex shrink-0 items-center">
                                <Link :href="route('dashboard')" class="flex items-center gap-2 group">
                                    <ApplicationLogo class="block h-8 w-auto transition-transform group-hover:scale-105" />
                                    <span class="font-bold text-xl tracking-tight hidden sm:block text-slate-800">
                                        Nexus<span class="text-indigo-600">App</span>
                                    </span>
                                </Link>
                            </div>

                            <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                                <NavLink :href="route('dashboard')" :active="route().current('dashboard')">
                                    Dashboard
                                </NavLink>

                                <template v-if="user.role === 'admin'">
                                    <NavLink :href="route('admin.dosen')" :active="route().current('admin.dosen')">
                                        Data Dosen
                                    </NavLink>
                                    <NavLink :href="route('admin.mahasiswa')" :active="route().current('admin.mahasiswa')">
                                        Mahasiswa & RFID
                                    </NavLink>
                                    <NavLink :href="route('admin.jadwal')" :active="route().current('admin.jadwal')">
                                        Jadwal Kuliah
                                    </NavLink>
                                    <NavLink :href="route('admin.krs')" :active="route().current('admin.krs')">
                                        Plotting KRS
                                    </NavLink>
                                    <NavLink :href="route('admin.ruangan')" :active="route().current('admin.ruangan')">
                                        Manajemen Ruangan
                                    </NavLink>
                                </template>

                                <template v-if="user.role === 'dosen'">
                                    <NavLink :href="route('dosen.jadwal')" :active="route().current('dosen.jadwal')">
                                        Jadwal & Laporan
                                    </NavLink>
                                    <NavLink :href="route('dosen.validasi')" :active="route().current('dosen.validasi')">
                                        Validasi Absensi
                                    </NavLink>
                                </template>
                            </div>
                        </div>

                        <div class="hidden sm:ms-6 sm:flex sm:items-center">
                            
                            <span class="mr-4 px-3 py-1 text-xs font-bold uppercase tracking-wider rounded-full border" 
                                  :class="user.role === 'admin' ? 'bg-rose-50 text-rose-600 border-rose-200' : 'bg-blue-50 text-blue-600 border-blue-200'">
                                {{ user.role }}
                            </span>

                            <div class="relative ms-3">
                                <Dropdown align="right" width="48">
                                    <template #trigger>
                                        <span class="inline-flex rounded-md">
                                            <button type="button" class="inline-flex items-center rounded-lg border border-transparent bg-white px-3 py-2 text-sm font-medium leading-4 text-gray-700 transition duration-150 ease-in-out hover:text-indigo-600 hover:bg-gray-50 focus:outline-none">
                                                {{ user.name }}
                                                <svg class="-me-0.5 ms-2 h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
                                        </span>
                                    </template>
                                    <template #content>
                                        <div class="bg-white border border-gray-100 rounded-md shadow-lg overflow-hidden">
                                            <DropdownLink :href="route('profile.edit')" class="text-gray-700 hover:bg-indigo-50 hover:text-indigo-700">Profile</DropdownLink>
                                            <DropdownLink :href="route('logout')" method="post" as="button" class="text-rose-600 hover:bg-rose-50 w-full text-left font-medium">Log Out</DropdownLink>
                                        </div>
                                    </template>
                                </Dropdown>
                            </div>
                        </div>

                        <div class="-me-2 flex items-center sm:hidden">
                            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-500 focus:bg-gray-100 focus:text-gray-500 focus:outline-none transition duration-150">
                                <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="sm:hidden bg-white border-b border-gray-200">
                    <div class="space-y-1 pb-3 pt-2">
                        <ResponsiveNavLink :href="route('dashboard')" :active="route().current('dashboard')">Dashboard</ResponsiveNavLink>
                        
                        <template v-if="user.role === 'admin'">
                            <ResponsiveNavLink :href="route('admin.dosen')" :active="route().current('admin.dosen')">Data Dosen</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.mahasiswa')" :active="route().current('admin.mahasiswa')">Mahasiswa & RFID</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.jadwal')" :active="route().current('admin.jadwal')">Jadwal Kuliah</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.krs')" :active="route().current('admin.krs')">Plotting KRS</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('admin.ruangan')" :active="route().current('admin.ruangan')">Manajemen Ruangan</ResponsiveNavLink>
                        </template>

                        <template v-if="user.role === 'dosen'">
                            <ResponsiveNavLink :href="route('dosen.jadwal')" :active="route().current('dosen.jadwal')">
                                Jadwal & Laporan
                            </ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('dosen.validasi')" :active="route().current('dosen.validasi')">
                                Validasi Absensi
                            </ResponsiveNavLink>
                        </template>
                    </div>

                    <div class="border-t border-gray-100 pb-1 pt-4 bg-gray-50">
                        <div class="px-4">
                            <div class="text-base font-medium text-gray-800">{{ user.name }} <span class="text-xs text-indigo-600 uppercase ml-1">({{ user.role }})</span></div>
                            <div class="text-sm font-medium text-gray-500">{{ user.email }}</div>
                        </div>
                        <div class="mt-3 space-y-1">
                            <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                            <ResponsiveNavLink :href="route('logout')" method="post" as="button" class="text-rose-600 font-medium">Log Out</ResponsiveNavLink>
                        </div>
                    </div>
                </div>
            </nav>

            <header class="bg-white border-b border-gray-100 shadow-sm" v-if="$slots.header">
                <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                    <slot name="header" />
                </div>
            </header>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>