<script setup>
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import Select from '@/Components/Select.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    orders: {
        type: Array,
        default: () => [],
    },
});

const statusFilter = ref('active');

const statusOptions = [
    { value: 'active', label: 'Active Orders' },
    { value: 'all', label: 'All Orders' },
    { value: 'pending', label: 'Pending' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
];

const filteredOrders = computed(() => {
    if (statusFilter.value === 'active') {
        return props.orders.filter((order) => !['completed', 'cancelled'].includes(order.status));
    }

    if (statusFilter.value === 'all') {
        return props.orders;
    }

    return props.orders.filter((order) => order.status === statusFilter.value);
});
</script>

<template>
    <AppLayout title="Orders">
        <Head title="Orders" />

        <Card title="All Orders">
            <template #actions>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <Select v-model="statusFilter" :options="statusOptions" class="w-full sm:w-40" />
                    <Link href="/orders/create">
                        <Button>New Order</Button>
                    </Link>
                </div>
            </template>

            <div v-if="filteredOrders.length" class="overflow-x-auto">
                <table class="w-full text-sm text-slate-700">
                    <thead class="border-y border-emerald-100 bg-emerald-50/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Items</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Payment</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Amount</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100/70">
                        <tr v-for="order in filteredOrders" :key="order.id" class="transition-colors duration-150 hover:bg-emerald-50/45">
                            <td class="px-4 py-3">
                                <Link :href="`/orders/${order.id}`" class="font-medium text-gray-900 hover:underline">
                                    #{{ order.order_number }}
                                </Link>
                            </td>
                            <td class="px-4 py-3">{{ order.customer?.phone_number }}</td>
                            <td class="px-4 py-3">{{ order.items?.length || 0 }}</td>
                            <td class="px-4 py-3"><Badge :status="order.status" /></td>
                            <td class="px-4 py-3 capitalize">{{ order.payment_method }}</td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">MVR {{ Number(order.total_amount || 0).toLocaleString() }}</td>
                            <td class="px-4 py-3 text-right">
                                <Link :href="`/orders/${order.id}/edit`">
                                    <Button variant="ghost" size="sm">Edit</Button>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="p-4 text-center text-gray-500">No orders found</div>
        </Card>
    </AppLayout>
</template>
