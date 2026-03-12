<template>
    <div class="relative flex min-h-screen items-center justify-center p-4 text-slate-900">
        <div class="pointer-events-none absolute inset-0 bg-[radial-gradient(circle_at_top,rgba(16,185,129,0.2),transparent_55%)]" />
        <div class="w-full max-w-md page-reveal">
            <div class="mb-6 text-center">
                <h1 class="text-2xl font-semibold text-slate-900">Raidhaan POS</h1>
                <p class="mt-1 text-sm text-slate-500">Order management</p>
            </div>
            <div class="panel-soft rounded-2xl p-6">
                <slot />
            </div>
        </div>
    </div>
</template>
