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
    { name: 'Deliveries', href: '/orders' },
    { name: 'Categories', href: '/categories' },
    { name: 'Items', href: '/items' },
    { name: 'Sales', href: '/sales' },
    { name: 'Settings', href: '/settings' },
];

const pageDescriptions = {
    Dashboard: 'Run the dining room from a live table board and keep an eye on open delivery work.',
    Deliveries: 'Track delivery and pickup tickets separately from dine-in table bills.',
    Categories: 'Manage the menu structure used to organize the item catalogue.',
    Items: 'Maintain item pricing and category assignment without extra clutter.',
    Sales: 'Review completed sales, payment mix, and date-filtered revenue.',
    Settings: 'Set the GST and service charge rules that should apply across the restaurant.',
    'Create Delivery': 'Capture delivery details, add items, and apply billing rules before dispatch.',
};

const description = computed(() => {
    if (props.title.startsWith('Edit Delivery #')) {
        return 'Update customer details, line items, payment, and charge settings before saving.';
    }

    if (props.title.startsWith('Delivery #')) {
        return 'Review the delivery ticket, print the receipt, and update fulfilment when needed.';
    }

    if (props.title.startsWith('Open ')) {
        return 'Attach menu items to the selected table, configure billing, and prepare the guest bill.';
    }

    if (props.title.startsWith('Edit Table ')) {
        return 'Update the open table bill before it is printed and locked for payment.';
    }

    if (props.title.endsWith(' Bill')) {
        return 'Review the table bill, print it with a unique bill number, then complete or void it.';
    }

    return pageDescriptions[props.title] || 'Manage restaurant tables, deliveries, billing, and sales from a single workspace.';
});

const isTablePage = computed(() => (
    props.title.startsWith('Open ')
    || props.title.startsWith('Edit Table ')
    || props.title.endsWith(' Bill')
));

const isActive = (href) => {
    if (href === '/') {
        return currentPath.value === '/' || isTablePage.value;
    }

    if (href === '/orders' && isTablePage.value) {
        return false;
    }

    return currentPath.value.startsWith(href);
};
</script>

<template>
    <div class="min-h-screen bg-slate-50 text-slate-900">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/95 backdrop-blur">
            <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col gap-4 py-4">
                    <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-slate-900 text-sm font-semibold text-white">
                                RP
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900">Raidhaan POS</p>
                                <p class="text-xs text-slate-500">Restaurant service and delivery management</p>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-3 lg:justify-end">
                            <div class="text-right">
                                <p class="text-sm font-medium text-slate-900">{{ auth.user?.name }}</p>
                                <p class="text-xs text-slate-500">Signed in</p>
                            </div>
                            <Link
                                href="/logout"
                                method="post"
                                as="button"
                                class="inline-flex items-center rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50"
                            >
                                Logout
                            </Link>
                        </div>
                    </div>

                    <nav class="flex flex-wrap gap-2">
                        <Link
                            v-for="item in navigation"
                            :key="item.href"
                            :href="item.href"
                            class="rounded-lg px-3 py-2 text-sm font-medium transition"
                            :class="isActive(item.href)
                                ? 'bg-slate-900 text-white'
                                : 'border border-slate-200 bg-white text-slate-600 hover:bg-slate-50 hover:text-slate-900'"
                        >
                            {{ item.name }}
                        </Link>
                    </nav>
                </div>
            </div>
        </header>

        <section class="border-b border-slate-200 bg-white">
            <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
                <h1 class="text-2xl font-semibold tracking-tight text-slate-900 sm:text-3xl">{{ title }}</h1>
                <p class="mt-2 max-w-3xl text-sm text-slate-500">{{ description }}</p>
            </div>
        </section>

        <div class="mx-auto max-w-7xl px-4 py-6 sm:px-6 lg:px-8">
            <div v-if="flash.success" class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                {{ flash.success }}
            </div>

            <div v-if="flash.error" class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ flash.error }}
            </div>

            <main>
                <slot />
            </main>
        </div>
    </div>
</template>
