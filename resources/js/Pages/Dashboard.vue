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
    tables: {
        type: Array,
        default: () => [],
    },
});

const formatCurrency = (value) => `MVR ${Number(value || 0).toLocaleString()}`;
</script>

<template>
    <AppLayout title="Dashboard">
        <Head title="Dashboard" />

        <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-5">
            <StatCard title="Tables" :value="stats.tableCount || 0" />
            <StatCard title="Occupied" :value="stats.occupiedCount || 0" />
            <StatCard title="Available" :value="stats.availableCount || 0" />
            <StatCard title="Bills Printed" :value="stats.printedCount || 0" />
            <StatCard title="Revenue" :value="`MVR ${(stats.totalRevenue || 0).toLocaleString()}`" />
        </div>

        <Card title="Dining Room Tables" description="Each table opens into an active guest bill or starts a new one when empty.">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 xl:grid-cols-4">
                <Link
                    v-for="table in tables"
                    :key="table.name"
                    :href="table.href"
                    class="group block rounded-2xl border border-slate-200 bg-white p-5 shadow-[0_1px_2px_rgba(15,23,42,0.04)] transition hover:-translate-y-0.5 hover:border-teal-200 hover:bg-teal-50/40 hover:shadow-md"
                >
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <p class="text-lg font-semibold tracking-tight text-slate-900">{{ table.name }}</p>
                            <p class="mt-1 text-sm text-slate-500">
                                {{ table.status === 'available' ? 'Available for a new bill.' : table.bill_number ? `Bill ${table.bill_number}` : 'Bill not printed yet.' }}
                            </p>
                        </div>
                        <Badge :status="table.status" />
                    </div>

                    <div v-if="table.status !== 'available'" class="mt-8 space-y-2">
                        <p class="text-sm text-slate-500">Ticket #{{ table.order_number }}</p>
                        <p class="text-sm text-slate-500">{{ table.item_count }} items attached</p>
                        <p class="text-xl font-semibold tracking-tight text-slate-900">{{ formatCurrency(table.total_amount) }}</p>
                    </div>

                    <div v-else class="mt-8 rounded-xl border border-dashed border-slate-300 bg-slate-50 px-4 py-6 text-center text-sm font-medium text-slate-700">
                        Open Table
                    </div>
                </Link>
            </div>
        </Card>
    </AppLayout>
</template>
