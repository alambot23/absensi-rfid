<script setup>
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';

defineProps({
    canResetPassword: { type: Boolean },
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in" />

    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-slate-950 bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-slate-900 via-slate-950 to-black">
        
        <div>
            <Link href="/">
                <ApplicationLogo class="w-20 h-20 fill-current text-indigo-400 drop-shadow-[0_0_15px_rgba(99,102,241,0.5)]" />
            </Link>
        </div>
        
        <h2 class="mt-4 text-2xl font-semibold text-slate-200 tracking-wide">Nexus<span class="text-indigo-400 font-bold">Attendance</span></h2>
        <p class="text-sm text-slate-500 mt-1 mb-6">Sistem Presensi RFID Terintegrasi</p>

        <div class="w-full sm:max-w-md mt-2 px-8 py-10 bg-slate-800/40 backdrop-blur-xl border border-slate-700/50 shadow-[0_8px_30px_rgb(0,0,0,0.12)] overflow-hidden sm:rounded-2xl">
            
            <div v-if="status" class="mb-4 font-medium text-sm text-green-400">
                {{ status }}
            </div>

            <form @submit.prevent="submit" class="space-y-5">
                <div>
                    <InputLabel for="email" value="Email" class="text-slate-300" />
                    <TextInput
                        id="email"
                        type="email"
                        class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-inner"
                        v-model="form.email"
                        required
                        autofocus
                        autocomplete="username"
                    />
                    <InputError class="mt-2 text-rose-400" :message="form.errors.email" />
                </div>

                <div>
                    <InputLabel for="password" value="Password" class="text-slate-300" />
                    <TextInput
                        id="password"
                        type="password"
                        class="mt-1 block w-full bg-slate-900/50 border-slate-700 text-slate-200 focus:border-indigo-500 focus:ring-indigo-500 rounded-lg shadow-inner"
                        v-model="form.password"
                        required
                        autocomplete="current-password"
                    />
                    <InputError class="mt-2 text-rose-400" :message="form.errors.password" />
                </div>

                <div class="block mt-4 flex items-center justify-between">
                    <label class="flex items-center">
                        <Checkbox name="remember" v-model:checked="form.remember" class="bg-slate-900 border-slate-700 text-indigo-500 focus:ring-indigo-500" />
                        <span class="ms-2 text-sm text-slate-400">Ingat Saya</span>
                    </label>

                    <Link
                        v-if="canResetPassword"
                        :href="route('password.request')"
                        class="text-sm text-indigo-400 hover:text-indigo-300 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 focus:ring-offset-slate-900 transition-colors"
                    >
                        Lupa password?
                    </Link>
                </div>

                <div class="pt-2">
                    <PrimaryButton class="w-full justify-center py-3 bg-indigo-600 hover:bg-indigo-500 text-white rounded-lg transition-all shadow-[0_0_15px_rgba(79,70,229,0.4)]" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                        Log In
                    </PrimaryButton>
                </div>
            </form>
        </div>
        
        <div class="mt-8 text-xs text-slate-600">
            &copy; 2026 Universitas Malikussaleh
        </div>
    </div>
</template>