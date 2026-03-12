<script setup>
import { Link, usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    title: {
        type: String,
        default: '',
    },
});

const page = usePage();

const auth = computed(() => page.props.auth || {});
const flash = computed(() => page.props.flash || {});
const currentPath = computed(() => {
    const url = page.url || '/';
    return url.split('?')[0];
});

const navigation = [
    { name: 'Dashboard', href: '/' },
    { name: 'Orders', href: '/orders' },
    { name: 'Categories', href: '/categories' },
    { name: 'Items', href: '/items' },
    { name: 'Sales', href: '/sales' },
];

const isActive = (href) => {
    if (href === '/') {
        return currentPath.value === '/';
    }

    return currentPath.value.startsWith(href);
};
</script>

<template>
    <div class="min-h-screen text-slate-900">
        <header class="panel-soft sticky top-0 z-10 border-b border-emerald-100/80 px-4 py-3 md:px-6">
            <div class="mx-auto flex w-full max-w-7xl flex-col gap-3 md:flex-row md:items-center md:justify-between">
                <div class="flex items-center gap-3">
                    <h1 class="text-lg font-semibold text-slate-900">{{ title }}</h1>
                </div>

                <nav class="flex flex-wrap gap-2">
                    <Link
                        v-for="item in navigation"
                        :key="item.href"
                        :href="item.href"
                        class="rounded-lg px-3 py-1.5 text-sm font-medium transition"
                        :class="isActive(item.href)
                            ? 'bg-emerald-100 text-emerald-900 ring-1 ring-emerald-200'
                            : 'text-slate-600 hover:bg-white/80 hover:text-slate-900'"
                    >
                        {{ item.name }}
                    </Link>
                </nav>

                <div class="flex items-center gap-3">
                    <p class="text-sm text-slate-600">{{ auth.user?.name }}</p>
                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="rounded-lg bg-white/80 px-3 py-1.5 text-sm font-medium text-slate-700 shadow-sm ring-1 ring-emerald-100 transition hover:bg-white"
                    >
                        Logout
                    </Link>
                </div>
            </div>
        </header>

        <div v-if="flash.success" class="mx-auto mt-4 w-full max-w-7xl rounded-2xl border border-emerald-200 bg-emerald-50/90 px-4 py-3 text-sm text-emerald-800">
            {{ flash.success }}
        </div>

        <div v-if="flash.error" class="mx-auto mt-4 w-full max-w-7xl rounded-2xl border border-rose-200 bg-rose-50/90 px-4 py-3 text-sm text-rose-700">
            {{ flash.error }}
        </div>

        <main class="mx-auto w-full max-w-7xl p-4 sm:p-6">
            <slot />
        </main>
    </div>
</template>
