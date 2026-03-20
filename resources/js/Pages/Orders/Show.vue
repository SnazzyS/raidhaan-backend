<script setup>
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
});

const isPrinting = ref(false);

const isTableBill = computed(() => props.order.delivery_type === 'dine_in');
const hasRealCustomer = computed(() => Number(props.order.customer?.phone_number || 0) !== 0);
const pageTitle = computed(() => (
    isTableBill.value
        ? `${props.order.table_name || 'Table'} Bill`
        : `Delivery #${props.order.order_number}`
));
const backHref = computed(() => (isTableBill.value ? '/' : '/orders'));
const printLabel = computed(() => (isTableBill.value ? 'Print Bill' : 'Print Receipt'));
const canEdit = computed(() => !isTableBill.value || !props.order.bill_printed_at);
const canCancel = computed(() => props.order.status === 'pending' && (!isTableBill.value || !props.order.bill_printed_at));
const canMarkPaid = computed(() => isTableBill.value && props.order.bill_printed_at && props.order.status !== 'completed' && props.order.status !== 'voided');
const canVoid = computed(() => isTableBill.value && props.order.bill_printed_at && props.order.status !== 'voided');

const formatCurrency = (value) => `MVR ${Number(value || 0).toLocaleString()}`;
const formatChargeLabel = (label, percentage, isInclusive) => `${label} (${Number(percentage || 0).toFixed(2)}%${isInclusive ? ', included' : ''})`;
const discountDisplay = computed(() => {
    if (!props.order.discount_type) {
        return 'No discount';
    }

    if (props.order.discount_type === 'percentage') {
        return `${Number(props.order.discount_value || 0).toFixed(2)}%`;
    }

    return formatCurrency(props.order.discount_value);
});

const requestStatusChange = (newStatus) => {
    const messages = {
        completed: isTableBill.value ? 'Mark this bill as paid?' : 'Mark this delivery as completed?',
        cancelled: 'Cancel this ticket?',
        voided: 'Void this printed bill? This cannot be treated as a cancellation.',
    };

    const confirmed = window.confirm(messages[newStatus] || 'Update ticket status?');

    if (!confirmed) {
        return;
    }

    router.patch(`/orders/${props.order.id}/status`, { status: newStatus }, { preserveScroll: true });
};

const handleReceiptPrint = () => {
    isPrinting.value = true;

    try {
        const receiptWindow = window.open(`/api/orders/${props.order.id}/receipt`, '_blank', 'noopener,noreferrer');

        if (!receiptWindow) {
            throw new Error('Popup blocked by browser. Please allow popups for this site and try again.');
        }

        window.setTimeout(() => {
            router.reload({ preserveScroll: true });
        }, 400);
    } catch (error) {
        // eslint-disable-next-line no-console
        console.error('Receipt print failed', error);
        const message = error?.message || 'Unknown error';
        window.alert(`Unable to open print window.\nError: ${message}`);
    } finally {
        isPrinting.value = false;
    }
};
</script>

<template>
    <AppLayout :title="pageTitle">
        <Head :title="pageTitle" />

        <div class="mb-6 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
            <Link :href="backHref">
                <Button variant="ghost">Back</Button>
            </Link>
            <div class="flex flex-wrap gap-2">
                <Button variant="secondary" :disabled="isPrinting" @click="handleReceiptPrint">
                    {{ isPrinting ? 'Opening...' : printLabel }}
                </Button>
                <Link v-if="canEdit" :href="`/orders/${order.id}/edit`">
                    <Button>Edit</Button>
                </Link>
            </div>
        </div>

        <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
            <div class="lg:col-span-2">
                <Card :title="isTableBill ? 'Bill Items' : 'Delivery Items'" description="A full breakdown of the items attached to this ticket.">
                    <div class="-mx-5 -mb-5 overflow-x-auto">
                        <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                            <thead class="bg-slate-50">
                                <tr>
                                    <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Item</th>
                                    <th class="px-5 py-3 text-center text-xs font-medium uppercase tracking-wide text-slate-500">Qty</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Price</th>
                                    <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Total</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-200 bg-white">
                                <tr v-for="item in order.items" :key="item.id" class="hover:bg-slate-50">
                                    <td class="px-5 py-4 font-medium text-slate-900">{{ item.name }}</td>
                                    <td class="px-5 py-4 text-center">{{ item.pivot.quantity }}</td>
                                    <td class="px-5 py-4 text-right">{{ formatCurrency(item.pivot.price) }}</td>
                                    <td class="px-5 py-4 text-right font-medium text-slate-900">{{ formatCurrency(Number(item.pivot.quantity) * Number(item.pivot.price)) }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="-mx-5 -mb-5 mt-5 space-y-3 border-t border-slate-200 bg-slate-50 px-5 py-4 text-sm">
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Menu total</span>
                            <span class="font-medium text-slate-900">{{ formatCurrency(order.subtotal_amount) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">Discount</span>
                            <span class="font-medium text-slate-900">- {{ formatCurrency(order.discount_amount) }}</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">{{ formatChargeLabel('GST', order.gst_percentage, order.gst_is_inclusive) }}</span>
                            <span class="font-medium text-slate-900">
                                {{ order.gst_is_inclusive ? `Included ${formatCurrency(order.gst_amount)}` : formatCurrency(order.gst_amount) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span class="text-slate-500">{{ formatChargeLabel('Service', order.service_charge_percentage, order.service_charge_is_inclusive) }}</span>
                            <span class="font-medium text-slate-900">
                                {{ order.service_charge_is_inclusive ? `Included ${formatCurrency(order.service_charge_amount)}` : formatCurrency(order.service_charge_amount) }}
                            </span>
                        </div>
                        <div class="flex items-center justify-between rounded-lg bg-white px-4 py-4">
                            <span class="font-medium text-slate-900">Grand total</span>
                            <span class="text-lg font-semibold text-slate-900">{{ formatCurrency(order.total_amount) }}</span>
                        </div>
                    </div>
                </Card>
            </div>

            <div class="space-y-6">
                <Card title="Status" description="Move the ticket through service using the rules for its current flow.">
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">Current status</span>
                            <Badge :status="order.status" />
                        </div>
                        <p v-if="isTableBill && !order.bill_printed_at" class="text-xs text-slate-500">
                            Table bills lock after printing. Once printed, they can be completed or voided, but not cancelled.
                        </p>
                        <div v-if="canMarkPaid || canVoid || canCancel" class="flex flex-wrap gap-2">
                            <Button v-if="canMarkPaid" class="flex-1" variant="success" size="sm" @click="requestStatusChange('completed')">
                                Mark Paid
                            </Button>
                            <Button v-if="canVoid" class="flex-1" variant="danger" size="sm" @click="requestStatusChange('voided')">
                                Void Bill
                            </Button>
                            <Button v-if="canCancel" class="flex-1" variant="danger" size="sm" @click="requestStatusChange('cancelled')">
                                Cancel
                            </Button>
                        </div>
                    </div>
                </Card>

                <Card
                    :title="isTableBill ? 'Guest' : 'Customer'"
                    :description="isTableBill ? 'Guest information captured for this table.' : 'Customer details captured with the delivery.'"
                >
                    <div class="space-y-3 text-sm">
                        <template v-if="hasRealCustomer">
                            <div class="flex items-start justify-between gap-4">
                                <span class="text-slate-500">Phone</span>
                                <span class="text-right font-medium text-slate-900">{{ order.customer?.phone_number || 'N/A' }}</span>
                            </div>
                            <div class="flex items-start justify-between gap-4">
                                <span class="text-slate-500">Address</span>
                                <span class="text-right font-medium text-slate-900">{{ order.customer?.address || 'N/A' }}</span>
                            </div>
                            <div class="flex items-start justify-between gap-4">
                                <span class="text-slate-500">City</span>
                                <span class="text-right font-medium capitalize text-slate-900">{{ order.customer?.city || 'N/A' }}</span>
                            </div>
                        </template>
                        <div v-else class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Guest</span>
                            <span class="text-right font-medium text-slate-900">Walk-in guest</span>
                        </div>
                    </div>
                </Card>

                <Card :title="isTableBill ? 'Bill Details' : 'Delivery Details'" description="Payment, billing, and service information for this ticket.">
                    <div class="space-y-3 text-sm">
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Ticket</span>
                            <span class="font-medium text-slate-900">#{{ order.order_number }}</span>
                        </div>
                        <div v-if="order.table_name" class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Table</span>
                            <span class="font-medium text-slate-900">{{ order.table_name }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Bill No.</span>
                            <span class="font-medium text-right text-slate-900">{{ order.bill_number || 'Assigned on first print' }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Service</span>
                            <span class="capitalize font-medium text-slate-900">{{ order.delivery_type === 'dine_in' ? 'Dine in' : order.delivery_type }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Payment</span>
                            <span class="capitalize font-medium text-slate-900">{{ order.payment_method }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Discount</span>
                            <span class="font-medium text-right text-slate-900">{{ discountDisplay }}</span>
                        </div>
                        <div v-if="order.transfer_reference_number" class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Reference</span>
                            <span class="font-mono text-right text-slate-900">{{ order.transfer_reference_number }}</span>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Printed</span>
                            <span class="font-medium text-right text-slate-900">
                                {{ order.bill_printed_at ? new Date(order.bill_printed_at).toLocaleString() : 'Not printed yet' }}
                            </span>
                        </div>
                        <div class="flex items-start justify-between gap-4">
                            <span class="text-slate-500">Created</span>
                            <span class="font-medium text-right text-slate-900">{{ new Date(order.created_at).toLocaleString() }}</span>
                        </div>
                    </div>
                </Card>
            </div>
        </div>
    </AppLayout>
</template>
