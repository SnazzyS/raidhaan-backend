<script setup>
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import Input from '@/Components/Input.vue';
import Select from '@/Components/Select.vue';
import StatCard from '@/Components/StatCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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
const paymentMethod = ref(props.filters.payment_method || '');
const deliveryType = ref(props.filters.delivery_type || '');

const paymentOptions = [
    { value: 'cash', label: 'Cash' },
    { value: 'card', label: 'Card' },
    { value: 'transfer', label: 'Transfer' },
];

const deliveryTypeOptions = [
    { value: 'dine_in', label: 'Dine In / Table' },
    { value: 'delivery', label: 'Delivery' },
    { value: 'pickup', label: 'Pickup' },
];

const handleFilter = () => {
    router.get('/sales', {
        from: fromDate.value,
        to: toDate.value,
        payment_method: paymentMethod.value,
        delivery_type: deliveryType.value,
    }, { preserveState: true, preserveScroll: true });
};

const handleClear = () => {
    fromDate.value = '';
    toDate.value = '';
    paymentMethod.value = '';
    deliveryType.value = '';
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

const hasActiveFilters = computed(() => fromDate.value || toDate.value || paymentMethod.value || deliveryType.value);
</script>

<template>
    <AppLayout title="Sales">
        <Head title="Sales" />

        <div class="mb-4 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard title="Sales" :value="stats.count || 0" />
            <StatCard title="Gross Sales" :value="`MVR ${(stats.grossTotal || 0).toLocaleString()}`" />
            <StatCard title="Discounts" :value="`MVR ${(stats.discountTotal || 0).toLocaleString()}`" />
            <StatCard title="Net Revenue" :value="`MVR ${(stats.total || 0).toLocaleString()}`" />
        </div>

        <div class="mb-6 grid grid-cols-1 gap-4 lg:grid-cols-2">
            <Card title="Payment Mix" description="See how completed sales were settled for the selected period.">
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Cash</span>
                        <span class="font-medium text-slate-900">MVR {{ Number(stats.cashTotal || 0).toLocaleString() }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Card</span>
                        <span class="font-medium text-slate-900">MVR {{ Number(stats.cardTotal || 0).toLocaleString() }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Transfer</span>
                        <span class="font-medium text-slate-900">MVR {{ Number(stats.transferTotal || 0).toLocaleString() }}</span>
                    </div>
                </div>
            </Card>

            <Card title="Service Mix" description="Track where revenue is coming from across dine-in and deliveries.">
                <div class="space-y-3 text-sm">
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Dine In / Table</span>
                        <span class="font-medium text-slate-900">{{ stats.dineInCount || 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Delivery</span>
                        <span class="font-medium text-slate-900">{{ stats.deliveryCount || 0 }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Pickup</span>
                        <span class="font-medium text-slate-900">{{ stats.pickupCount || 0 }}</span>
                    </div>
                </div>
            </Card>
        </div>

        <Card title="Sales History" description="Filter completed sales by date, service type, and payment method.">
            <template #actions>
                <div class="flex flex-wrap items-center gap-2">
                    <Input v-model="fromDate" type="date" class="w-full sm:w-36" />
                    <span class="hidden text-slate-400 sm:inline">to</span>
                    <Input v-model="toDate" type="date" class="w-full sm:w-36" />
                    <Select v-model="deliveryType" :options="deliveryTypeOptions" placeholder="All services" class="w-full sm:w-44" />
                    <Select v-model="paymentMethod" :options="paymentOptions" placeholder="All payments" class="w-full sm:w-40" />
                    <Button class="w-full sm:w-auto" @click="handleFilter">Filter</Button>
                    <Button v-if="hasActiveFilters" variant="ghost" class="w-full sm:w-auto" @click="handleClear">Clear</Button>
                </div>
            </template>

            <div v-if="sales.length" class="-mx-5 -mb-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Order</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Type</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Date</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Payment</th>
                            <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Gross</th>
                            <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Discount</th>
                            <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Net</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <tr v-for="sale in sales" :key="sale.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-medium text-slate-900">#{{ sale.order_number }}</td>
                            <td class="px-5 py-4">
                                <Badge variant="default">
                                    {{ sale.delivery_type === 'dine_in' ? 'Dine In' : sale.delivery_type }}
                                </Badge>
                            </td>
                            <td class="px-5 py-4">{{ new Date(sale.completed_at || sale.created_at).toLocaleDateString() }}</td>
                            <td class="px-5 py-4">
                                <Badge :variant="paymentVariant(sale.payment_method)">
                                    {{ sale.payment_method }}
                                </Badge>
                            </td>
                            <td class="px-5 py-4 text-right font-medium text-slate-900">
                                MVR {{ Number(sale.subtotal || 0).toLocaleString() }}
                            </td>
                            <td class="px-5 py-4 text-right font-medium text-slate-900">
                                - MVR {{ Number(sale.discount_amount || 0).toLocaleString() }}
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
