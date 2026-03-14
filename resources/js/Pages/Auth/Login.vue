<script setup>
import Button from '@/Components/Button.vue';
import Input from '@/Components/Input.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
});

const submit = () => {
    form.post('/login');
};
</script>

<template>
    <GuestLayout>
        <Head title="Login" />

        <div class="mb-6">
            <h2 class="text-xl font-semibold tracking-tight text-slate-900">Sign in</h2>
            <p class="mt-2 text-sm text-slate-500">
                Use your account to manage orders, catalogue updates, and daily sales.
            </p>
        </div>

        <form class="space-y-4" @submit.prevent="submit">
            <Input
                v-model="form.email"
                label="Email"
                type="email"
                placeholder="your@email.com"
                :error="form.errors.email"
            />

            <Input
                v-model="form.password"
                label="Password"
                type="password"
                placeholder="••••••••"
                :error="form.errors.password"
            />

            <Button type="submit" class="w-full" :disabled="form.processing">
                {{ form.processing ? 'Signing in...' : 'Sign in' }}
            </Button>
        </form>
    </GuestLayout>
</template>
