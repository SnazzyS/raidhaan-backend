<script setup>
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import Input from '@/Components/Input.vue';
import Select from '@/Components/Select.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { calculateBillTotals } from '@/utils/billing';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    order: {
        type: Object,
        required: true,
    },
    items: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
    tables: {
        type: Array,
        default: () => [],
    },
    settings: {
        type: Object,
        default: () => ({}),
    },
});

const form = useForm({
    phone_number: props.order.customer?.phone_number && Number(props.order.customer.phone_number) !== 0
        ? props.order.customer.phone_number.toString()
        : '',
    address: props.order.customer?.phone_number && Number(props.order.customer.phone_number) !== 0
        ? props.order.customer?.address || ''
        : '',
    city: props.order.customer?.phone_number && Number(props.order.customer.phone_number) !== 0
        ? props.order.customer?.city || ''
        : '',
    order: {
        status: props.order.status || 'pending',
        delivery_type: props.order.delivery_type || 'delivery',
        table_name: props.order.table_name || '',
        payment_method: props.order.payment_method || 'cash',
        transfer_reference_number: props.order.transfer_reference_number || '',
        items: props.order.items?.map((item) => ({
            item_id: item.id,
            quantity: Number(item.pivot.quantity),
            name: item.name,
            price: Number(item.pivot.price),
        })) || [],
    },
});

const selectedCategory = ref('');
const searchTerm = ref('');

const categoryOptions = computed(() =>
    props.categories.map((category) => ({ value: String(category.id), label: category.name })),
);

const cityOptions = [
    { value: 'male', label: 'Male' },
    { value: 'hulhumale phase 1', label: 'Hulhumale Phase 1' },
    { value: 'hulhumale phase 2', label: 'Hulhumale Phase 2' },
];

const serviceTypeOptions = [
    { value: 'delivery', label: 'Delivery' },
    { value: 'pickup', label: 'Pickup' },
    { value: 'dine_in', label: 'Dine In / Table' },
];

const paymentOptions = [
    { value: 'cash', label: 'Cash' },
    { value: 'transfer', label: 'Transfer' },
    { value: 'card', label: 'Card' },
];

const filteredItems = computed(() => {
    const query = searchTerm.value.trim().toLowerCase();

    return props.items.filter((item) => {
        const matchesCategory = !selectedCategory.value || item.category_id === Number(selectedCategory.value);
        const matchesSearch = query === ''
            || item.name.toLowerCase().includes(query)
            || (item.category?.name || '').toLowerCase().includes(query);

        return matchesCategory && matchesSearch;
    });
});

const isTableBill = computed(() => form.order.delivery_type === 'dine_in');
const isLockedTableContext = computed(() => props.order.delivery_type === 'dine_in');
const isTransfer = computed(() => form.order.payment_method === 'transfer');
const pageTitle = computed(() => {
    if (isTableBill.value) {
        return form.order.table_name ? `Edit ${form.order.table_name}` : 'Edit Table Bill';
    }

    return `Edit Delivery #${props.order.order_number}`;
});

const totals = computed(() => calculateBillTotals(
    form.order.items,
    props.settings.gst_percentage,
    props.settings.gst_is_inclusive,
    props.settings.service_charge_percentage,
    props.settings.service_charge_is_inclusive,
));

const totalItems = computed(() => form.order.items.length);
const totalQuantity = computed(() => form.order.items.reduce((sum, item) => sum + Number(item.quantity), 0));

watch(() => form.order.payment_method, (paymentMethod) => {
    if (paymentMethod !== 'transfer') {
        form.order.transfer_reference_number = '';
    }
});

watch(() => form.order.delivery_type, (deliveryType) => {
    if (deliveryType !== 'dine_in') {
        form.order.table_name = '';
    }
});

const addItem = (item) => {
    const existingIndex = form.order.items.findIndex((selectedItem) => selectedItem.item_id === item.id);

    if (existingIndex > -1) {
        form.order.items[existingIndex].quantity += 1;
        return;
    }

    form.order.items.push({
        item_id: item.id,
        quantity: 1,
        name: item.name,
        price: Number(item.price),
    });
};

const removeItem = (index) => {
    form.order.items.splice(index, 1);
};

const updateQuantity = (index, quantity) => {
    if (quantity < 1) {
        removeItem(index);
        return;
    }

    form.order.items[index].quantity = quantity;
};

const chargeLabel = (label, percentage, isInclusive) => `${label} (${Number(percentage || 0).toFixed(2)}%${isInclusive ? ', included' : ''})`;

const submit = () => {
    form.put(`/orders/${props.order.id}`);
};
</script>

<template>
    <AppLayout :title="pageTitle">
        <Head :title="pageTitle" />

        <div class="mb-6">
            <Link :href="`/orders/${order.id}`">
                <Button variant="ghost">Back</Button>
            </Link>
        </div>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
                <div class="space-y-6 lg:col-span-2">
                    <Card
                        v-if="!isTableBill"
                        title="Customer"
                        description="Update delivery or pickup details for this ticket."
                    >
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <Input v-model="form.phone_number" label="Phone" placeholder="7123456" :error="form.errors.phone_number" />
                            <Input v-model="form.address" label="Address" placeholder="House, street" :error="form.errors.address" />
                            <Select v-model="form.city" label="City" :options="cityOptions" placeholder="Select city" :error="form.errors.city" />
                        </div>
                    </Card>

                    <Card title="Items" description="Add more menu items or adjust the current selection.">
                        <template #actions>
                            <div class="flex w-full flex-col gap-2 sm:w-auto sm:flex-row">
                                <Input
                                    v-model="searchTerm"
                                    placeholder="Search items"
                                    class="w-full sm:w-52"
                                />
                                <Select
                                    v-model="selectedCategory"
                                    :options="categoryOptions"
                                    placeholder="All"
                                    class="w-full sm:w-44"
                                />
                            </div>
                        </template>

                        <div v-if="filteredItems.length" class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-3">
                            <button
                                v-for="item in filteredItems"
                                :key="item.id"
                                type="button"
                                class="rounded-lg border border-slate-200 bg-white p-4 text-left transition hover:border-teal-200 hover:bg-teal-50/40"
                                @click="addItem(item)"
                            >
                                <p class="truncate text-sm font-medium text-slate-900">{{ item.name }}</p>
                                <p class="mt-1 text-xs text-slate-500">{{ item.category?.name || 'Uncategorized' }}</p>
                                <p class="mt-4 text-sm font-semibold text-slate-900">MVR {{ Number(item.price || 0).toLocaleString() }}</p>
                            </button>
                        </div>
                        <div
                            v-else
                            class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-sm text-slate-500"
                        >
                            No items match the current search or category filter.
                        </div>
                    </Card>

                    <Card title="Selected Items" description="Keep the ticket accurate before saving your changes.">
                        <div v-if="form.order.items.length" class="space-y-3">
                            <div
                                v-for="(item, index) in form.order.items"
                                :key="`${item.item_id}-${index}`"
                                class="flex flex-col gap-4 rounded-lg border border-slate-200 bg-slate-50 p-4 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div>
                                    <p class="text-sm font-medium text-slate-900">{{ item.name }}</p>
                                    <p class="mt-1 text-xs text-slate-500">Unit price: MVR {{ Number(item.price || 0).toLocaleString() }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="flex items-center gap-2">
                                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 bg-white text-sm font-medium text-slate-600 hover:bg-slate-100" @click="updateQuantity(index, item.quantity - 1)">-</button>
                                        <span class="w-8 text-center text-sm font-medium text-slate-900">{{ item.quantity }}</span>
                                        <button type="button" class="flex h-8 w-8 items-center justify-center rounded-md border border-slate-300 bg-white text-sm font-medium text-slate-600 hover:bg-slate-100" @click="updateQuantity(index, item.quantity + 1)">+</button>
                                    </div>
                                    <span class="min-w-24 text-right text-sm font-semibold text-slate-900">
                                        MVR {{ (item.price * item.quantity).toLocaleString() }}
                                    </span>
                                    <button type="button" class="rounded-md px-2 py-1 text-sm font-medium text-rose-600 transition hover:bg-rose-50 hover:text-rose-700" @click="removeItem(index)">
                                        Remove
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div
                            v-else
                            class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-6 py-10 text-center text-sm text-slate-500"
                        >
                            This ticket does not have any items yet.
                        </div>
                    </Card>
                </div>

                <div class="space-y-6 lg:sticky lg:top-6 lg:self-start">
                    <Card :title="isTableBill ? 'Table Setup' : 'Delivery Setup'" description="Update how this ticket will be served and paid.">
                        <div class="space-y-4">
                            <template v-if="isLockedTableContext">
                                <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Service Type</p>
                                    <p class="mt-1 font-medium text-slate-900">Dine In / Table</p>
                                </div>
                                <div class="rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm">
                                    <p class="text-xs font-medium uppercase tracking-wide text-slate-500">Table</p>
                                    <p class="mt-1 font-medium text-slate-900">{{ form.order.table_name }}</p>
                                </div>
                            </template>
                            <template v-else>
                                <Select
                                    v-model="form.order.delivery_type"
                                    label="Service Type"
                                    :options="serviceTypeOptions"
                                    :error="form.errors['order.delivery_type']"
                                />
                                <Select
                                    v-if="isTableBill"
                                    v-model="form.order.table_name"
                                    label="Table"
                                    :options="tables"
                                    placeholder="Select table"
                                    :error="form.errors['order.table_name']"
                                />
                            </template>
                            <Select
                                v-model="form.order.payment_method"
                                label="Payment"
                                :options="paymentOptions"
                                :error="form.errors['order.payment_method']"
                            />
                            <Input
                                v-if="isTransfer"
                                v-model="form.order.transfer_reference_number"
                                label="Transfer Reference"
                                placeholder="Reference number"
                                :error="form.errors['order.transfer_reference_number']"
                            />
                        </div>
                    </Card>

                    <Card title="Summary" description="Review the bill totals before saving. GST and service charge come from Settings.">
                        <template #actions>
                            <Link href="/settings" class="text-sm font-medium text-teal-700 hover:text-teal-800">
                                Open settings
                            </Link>
                        </template>
                        <div class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Items</span>
                                <span class="font-medium text-slate-900">{{ totalItems }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Quantity</span>
                                <span class="font-medium text-slate-900">{{ totalQuantity }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">Menu total</span>
                                <span class="font-medium text-slate-900">MVR {{ totals.subtotalAmount.toLocaleString() }}</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">{{ chargeLabel('GST', props.settings.gst_percentage, props.settings.gst_is_inclusive) }}</span>
                                <span class="font-medium text-slate-900">
                                    {{ props.settings.gst_is_inclusive ? `Included MVR ${totals.gstAmount.toLocaleString()}` : `MVR ${totals.gstAmount.toLocaleString()}` }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-slate-500">{{ chargeLabel('Service', props.settings.service_charge_percentage, props.settings.service_charge_is_inclusive) }}</span>
                                <span class="font-medium text-slate-900">
                                    {{ props.settings.service_charge_is_inclusive ? `Included MVR ${totals.serviceChargeAmount.toLocaleString()}` : `MVR ${totals.serviceChargeAmount.toLocaleString()}` }}
                                </span>
                            </div>
                            <div class="flex items-center justify-between rounded-lg bg-slate-50 px-4 py-4">
                                <span class="font-medium text-slate-900">Grand total</span>
                                <span class="text-lg font-semibold text-slate-900">MVR {{ totals.totalAmount.toLocaleString() }}</span>
                            </div>
                        </div>

                        <Button type="submit" class="mt-5 w-full" :disabled="form.processing || !form.order.items.length">
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                    </Card>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
