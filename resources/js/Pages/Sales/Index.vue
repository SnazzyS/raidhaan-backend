<script setup>
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import Input from '@/Components/Input.vue';
import StatCard from '@/Components/StatCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    sales: {
        type: Array,
        default: () => [],
    },
    stats: {
        type: Object,
        default: () => ({}),
    },
    filters: {
        type: Object,
        default: () => ({}),
    },
});

const fromDate = ref(props.filters.from || '');
const toDate = ref(props.filters.to || '');

const handleFilter = () => {
    router.get('/sales', { from: fromDate.value, to: toDate.value }, { preserveState: true, preserveScroll: true });
};

const handleClear = () => {
    fromDate.value = '';
    toDate.value = '';
    router.get('/sales', {}, { preserveState: true });
};

const paymentVariant = (paymentMethod) => {
    if (paymentMethod === 'cash') {
        return 'success';
    }

    if (paymentMethod === 'transfer') {
        return 'info';
    }

    return 'primary';
};
</script>

<template>
    <AppLayout title="Sales">
        <Head title="Sales" />

        <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <StatCard title="Sales" :value="stats.count || 0" />
            <StatCard title="Revenue" :value="`MVR ${(stats.total || 0).toLocaleString()}`" />
            <StatCard title="Cash" :value="`MVR ${(stats.cashTotal || 0).toLocaleString()}`" />
        </div>

        <Card title="Sales History" description="Filter completed sales by date and review payment mix.">
            <template #actions>
                <div class="flex flex-wrap items-center gap-2">
                    <Input v-model="fromDate" type="date" class="w-full sm:w-36" />
                    <span class="hidden text-slate-400 sm:inline">to</span>
                    <Input v-model="toDate" type="date" class="w-full sm:w-36" />
                    <Button class="w-full sm:w-auto" @click="handleFilter">Filter</Button>
                    <Button v-if="fromDate || toDate" variant="ghost" class="w-full sm:w-auto" @click="handleClear">Clear</Button>
                </div>
            </template>

            <div v-if="sales.length" class="-mx-5 -mb-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Order</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Payment</th>
                            <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <tr v-for="sale in sales" :key="sale.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-medium text-slate-900">#{{ sale.order_number }}</td>
                            <td class="px-5 py-4">{{ new Date(sale.created_at).toLocaleDateString() }}</td>
                            <td class="px-5 py-4">
                                <Badge :variant="paymentVariant(sale.payment_method)">
                                    {{ sale.payment_method }}
                                </Badge>
                            </td>
                            <td class="px-5 py-4 text-right font-medium text-slate-900">
                                MVR {{ Number(sale.total || 0).toLocaleString() }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                v-else
                class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center text-sm text-slate-500"
            >
                No sales found for the selected dates.
            </div>
        </Card>
    </AppLayout>
</template>
