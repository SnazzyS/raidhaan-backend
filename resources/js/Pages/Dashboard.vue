<script setup>
import Badge from '@/Components/Badge.vue';
import Card from '@/Components/Card.vue';
import StatCard from '@/Components/StatCard.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    stats: {
        type: Object,
        default: () => ({}),
    },
    recentOrders: {
        type: Array,
        default: () => [],
    },
});
</script>

<template>
    <AppLayout title="Dashboard">
        <Head title="Dashboard" />

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
            <StatCard title="Total Orders" :value="stats.totalOrders || 0" />
            <StatCard title="Pending" :value="stats.pendingOrders || 0" />
            <StatCard title="Revenue" :value="`MVR ${(stats.totalRevenue || 0).toLocaleString()}`" />
            <StatCard title="Customers" :value="stats.totalCustomers || 0" />
        </div>

        <Card title="Recent Orders">
            <template #actions>
                <Link href="/orders" class="text-sm text-gray-500 hover:text-gray-700">
                    View all →
                </Link>
            </template>

            <div v-if="recentOrders.length" class="overflow-x-auto">
                <table class="w-full text-sm text-slate-700">
                    <thead class="border-y border-emerald-100 bg-emerald-50/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Order</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Customer</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Amount</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100/70">
                        <tr v-for="order in recentOrders" :key="order.id" class="transition-colors duration-150 hover:bg-emerald-50/45">
                            <td class="px-4 py-3">
                                <Link :href="`/orders/${order.id}`" class="font-medium text-gray-900 hover:underline">
                                    #{{ order.order_number }}
                                </Link>
                            </td>
                            <td class="px-4 py-3">{{ order.customer?.phone_number }}</td>
                            <td class="px-4 py-3"><Badge :status="order.status" /></td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">
                                MVR {{ Number(order.total_amount || 0).toLocaleString() }}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="p-4 text-center text-gray-500">No recent orders</div>
        </Card>
    </AppLayout>
</template>
