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

        <h2 class="mb-4 text-lg font-medium text-gray-900">Sign in</h2>

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
