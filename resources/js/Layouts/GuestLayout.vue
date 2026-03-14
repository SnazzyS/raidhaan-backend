<template>
    <div class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-12 text-slate-900">
        <div class="w-full max-w-md">
            <div class="mb-8 text-center">
                <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-xl bg-slate-900 text-sm font-semibold text-white">
                    RP
                </div>
                <h1 class="mt-4 text-2xl font-semibold tracking-tight text-slate-900">Raidhaan POS</h1>
                <p class="mt-2 text-sm text-slate-500">Local order management</p>
            </div>
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm sm:p-8">
                <slot />
            </div>
        </div>
    </div>
</template>
