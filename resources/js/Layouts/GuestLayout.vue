<script setup>
import logo from '@/assets/images/logo.png';
</script>

<template>
    <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-12 text-slate-900">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <img :src="logo" alt="Raidhaan POS" class="mx-auto h-12 w-12 rounded-xl object-cover" />
                <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">Raidhaan POS</h1>
                <p class="mt-2 text-sm text-slate-500">Local order management</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8">
                <slot />
            </div>
        </div>
    </div>
</template>
