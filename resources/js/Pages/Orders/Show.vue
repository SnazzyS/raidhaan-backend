<script setup>
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { printReceiptHtml } from '@/lib/qz';
import { Head, Link, router } from '@inertiajs/vue3';
import axios from 'axios';
import { ref } from 'vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const isPrinting = ref(false);

const requestStatusChange = (newStatus) => {
    const isCancel = newStatus === 'cancelled';
    const confirmed = window.confirm(
        isCancel ? 'This will set the order status to Cancelled. Continue?' : 'This will set the order status to Completed. Continue?',
    );

    if (!confirmed) {
        return;
    }

    router.patch(`/orders/${props.order.id}/status`, { status: newStatus }, { preserveScroll: true });
};

const handleReceiptPrint = async () => {
    isPrinting.value = true;

    try {
        const response = await axios.get(`/api/orders/${props.order.id}/receipt`, {
            params: { qz: 1 },
            responseType: 'text',
        });

        await printReceiptHtml(response.data, { widthMm: 80 });
    } catch (error) {
        // eslint-disable-next-line no-console
        console.error('Receipt print failed', error);
        const msg = error?.message || 'Unknown error';
        window.alert(`Unable to print via QZ Tray.\nError: ${msg}\n\nMake sure QZ Tray is running and trusted.`);
    } finally {
        isPrinting.value = false;
    }
};
</script>

<template>
    <AppLayout :title="`Order #${order.order_number}`">
        <Head :title="`Order #${order.order_number}`" />

        <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <Link href="/orders">
                <Button variant="ghost">Back</Button>
            </Link>
            <div class="flex flex-wrap gap-2">
                <Button variant="secondary" :disabled="isPrinting" @click="handleReceiptPrint">
                    {{ isPrinting ? 'Printing...' : 'Print (QZ)' }}
                </Button>
                <a :href="`/api/orders/${order.id}/receipt`" target="_blank" rel="noopener noreferrer">
                    <Button variant="ghost">Browser Print</Button>
                </a>
                <Link :href="`/orders/${order.id}/edit`">
                    <Button>Edit</Button>
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <Card title="Items">
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-slate-700">
                            <thead class="border-y border-emerald-100 bg-emerald-50/60">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Item</th>
                                    <th class="px-4 py-3 text-center text-xs font-semibold uppercase tracking-wider text-slate-500">Qty</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Price</th>
                                    <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-emerald-100/70">
                                <tr v-for="item in order.items" :key="item.id" class="transition-colors duration-150 hover:bg-emerald-50/45">
                                    <td class="px-4 py-3 font-medium text-gray-900">{{ item.name }}</td>
                                    <td class="px-4 py-3 text-center">{{ item.pivot.quantity }}</td>
                                    <td class="px-4 py-3 text-right">MVR {{ Number(item.pivot.price).toLocaleString() }}</td>
                                    <td class="px-4 py-3 text-right font-medium text-gray-900">MVR {{ (Number(item.pivot.quantity) * Number(item.pivot.price)).toLocaleString() }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="mt-3 flex justify-between border-t border-gray-200 px-1 pt-3">
                        <span class="font-medium text-gray-900">Total</span>
                        <span class="text-lg font-semibold text-gray-900">MVR {{ Number(order.total_amount || 0).toLocaleString() }}</span>
                    </div>
                </Card>
            </div>

            <div class="space-y-4">
                <Card title="Status">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-gray-500">Current</span>
                            <Badge :status="order.status" />
                        </div>
                        <div v-if="order.status === 'pending'" class="flex gap-2">
                            <Button class="flex-1" variant="success" size="sm" @click="requestStatusChange('completed')">
                                Complete
                            </Button>
                            <Button class="flex-1" variant="danger" size="sm" @click="requestStatusChange('cancelled')">
                                Cancel
                            </Button>
                        </div>
                    </div>
                </Card>

                <Card title="Customer">
                    <div class="space-y-2 text-sm">
                        <div><span class="text-gray-500">Phone: </span><span class="text-gray-900">{{ order.customer?.phone_number }}</span></div>
                        <div><span class="text-gray-500">Address: </span><span class="text-gray-900">{{ order.customer?.address }}</span></div>
                        <div><span class="text-gray-500">City: </span><span class="text-gray-900 capitalize">{{ order.customer?.city }}</span></div>
                    </div>
                </Card>

                <Card title="Info">
                    <div class="space-y-2 text-sm">
                        <div class="flex justify-between"><span class="text-gray-500">Delivery</span><span class="capitalize text-gray-900">{{ order.delivery_type }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Payment</span><span class="capitalize text-gray-900">{{ order.payment_method }}</span></div>
                        <div v-if="order.transfer_reference_number" class="flex justify-between"><span class="text-gray-500">Reference</span><span class="font-mono text-gray-900">{{ order.transfer_reference_number }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-500">Created</span><span class="text-gray-900">{{ new Date(order.created_at).toLocaleDateString() }}</span></div>
                    </div>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
