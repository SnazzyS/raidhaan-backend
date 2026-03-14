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
    { value: 'active', label: 'Active Deliveries' },
    { value: 'all', label: 'All Deliveries' },
    { value: 'pending', label: 'Pending' },
    { value: 'completed', label: 'Completed' },
    { value: 'cancelled', label: 'Cancelled' },
    { value: 'voided', label: 'Voided' },
];

const filteredOrders = computed(() => {
    if (statusFilter.value === 'active') {
        return props.orders.filter((order) => !['completed', 'cancelled', 'voided'].includes(order.status));
    }

    if (statusFilter.value === 'all') {
        return props.orders;
    }

    return props.orders.filter((order) => order.status === statusFilter.value);
});
</script>

<template>
    <AppLayout title="Deliveries">
        <Head title="Deliveries" />

        <Card title="Deliveries" description="Review delivery and pickup tickets separately from dine-in table bills.">
            <template #actions>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <Select v-model="statusFilter" :options="statusOptions" class="w-full sm:w-40" />
                    <Link href="/orders/create">
                        <Button>New Delivery</Button>
                    </Link>
                </div>
            </template>

            <div v-if="filteredOrders.length" class="-mx-5 -mb-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Delivery</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Type</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Customer</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Items</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Status</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Payment</th>
                            <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Amount</th>
                            <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <tr v-for="order in filteredOrders" :key="order.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4">
                                <Link :href="`/orders/${order.id}`" class="font-medium text-slate-900 hover:text-slate-700">
                                    #{{ order.order_number }}
                                </Link>
                            </td>
                            <td class="px-5 py-4 capitalize">{{ order.delivery_type }}</td>
                            <td class="px-5 py-4">{{ order.customer?.phone_number }}</td>
                            <td class="px-5 py-4">{{ order.items?.length || 0 }}</td>
                            <td class="px-5 py-4"><Badge :status="order.status" /></td>
                            <td class="px-5 py-4 capitalize">{{ order.payment_method }}</td>
                            <td class="px-5 py-4 text-right font-medium text-slate-900">MVR {{ Number(order.total_amount || 0).toLocaleString() }}</td>
                            <td class="px-5 py-4 text-right">
                                <Link :href="`/orders/${order.id}/edit`">
                                    <Button variant="ghost" size="sm">Edit</Button>
                                </Link>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                v-else
                class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center text-sm text-slate-500"
            >
                No deliveries found for this filter.
            </div>
        </Card>
    </AppLayout>
</template>
