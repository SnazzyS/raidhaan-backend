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
</script>

<template>
    <AppLayout title="Sales">
        <Head title="Sales" />

        <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <StatCard title="Sales" :value="stats.count || 0" />
            <StatCard title="Revenue" :value="`MVR ${(stats.total || 0).toLocaleString()}`" />
            <StatCard title="Cash" :value="`MVR ${(stats.cashTotal || 0).toLocaleString()}`" />
        </div>

        <Card title="Sales History">
            <template #actions>
                <div class="flex flex-wrap items-center gap-2">
                    <Input v-model="fromDate" type="date" class="w-full sm:w-36" />
                    <span class="hidden text-gray-400 sm:inline">to</span>
                    <Input v-model="toDate" type="date" class="w-full sm:w-36" />
                    <Button class="w-full sm:w-auto" @click="handleFilter">Filter</Button>
                    <Button v-if="fromDate || toDate" variant="ghost" class="w-full sm:w-auto" @click="handleClear">Clear</Button>
                </div>
            </template>

            <div v-if="sales.length" class="overflow-x-auto">
                <table class="w-full text-sm text-slate-700">
                    <thead class="border-y border-emerald-100 bg-emerald-50/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Date</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Payment</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100/70">
                        <tr v-for="sale in sales" :key="sale.id" class="transition-colors duration-150 hover:bg-emerald-50/45">
                            <td class="px-4 py-3 font-medium text-gray-900">#{{ sale.order_number }}</td>
                            <td class="px-4 py-3">{{ new Date(sale.created_at).toLocaleDateString() }}</td>
                            <td class="px-4 py-3">
                                <Badge :variant="sale.payment_method === 'cash' ? 'success' : 'primary'">
                                    {{ sale.payment_method }}
                                </Badge>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">
                                MVR {{ Number(sale.total || 0).toLocaleString() }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="p-4 text-center text-gray-500">No sales found</div>
        </Card>
    </AppLayout>
</template>
