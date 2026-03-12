<script setup>
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import Input from '@/Components/Input.vue';
import Select from '@/Components/Select.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

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
});

const form = useForm({
    phone_number: props.order.customer?.phone_number?.toString() || '',
    address: props.order.customer?.address || '',
    city: props.order.customer?.city || '',
    order: {
        status: props.order.status || 'pending',
        delivery_type: props.order.delivery_type || 'delivery',
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

const categoryOptions = computed(() =>
    props.categories.map((cat) => ({ value: String(cat.id), label: cat.name })),
);

const cityOptions = [
    { value: 'male', label: 'Male' },
    { value: 'hulhumale phase 1', label: 'Hulhumale Phase 1' },
    { value: 'hulhumale phase 2', label: 'Hulhumale Phase 2' },
];

const filteredItems = computed(() => {
    if (!selectedCategory.value) {
        return props.items;
    }

    return props.items.filter((item) => item.category_id === Number(selectedCategory.value));
});

const addItem = (item) => {
    const existingIndex = form.order.items.findIndex((i) => i.item_id === item.id);

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

const totalItems = computed(() => form.order.items.length);
const totalQuantity = computed(() => form.order.items.reduce((sum, item) => sum + Number(item.quantity), 0));
const totalAmount = computed(() =>
    form.order.items.reduce((sum, item) => sum + (Number(item.price) * Number(item.quantity)), 0),
);

const submit = () => {
    form.put(`/orders/${props.order.id}`);
};
</script>

<template>
    <AppLayout :title="`Edit Order #${order.order_number}`">
        <Head :title="`Edit Order #${order.order_number}`" />

        <div class="mb-4">
            <Link :href="`/orders/${order.id}`">
                <Button variant="ghost">← Back</Button>
            </Link>
        </div>

        <form @submit.prevent="submit">
            <div class="grid grid-cols-1 gap-4 lg:grid-cols-3">
                <div class="space-y-4 lg:col-span-2">
                    <Card title="Customer">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-3">
                            <Input v-model="form.phone_number" label="Phone" placeholder="7123456" :error="form.errors.phone_number" />
                            <Input v-model="form.address" label="Address" placeholder="House, street" :error="form.errors.address" />
                            <Select v-model="form.city" label="City" :options="cityOptions" placeholder="Select city" :error="form.errors.city" />
                        </div>
                    </Card>

                    <Card title="Items">
                        <template #actions>
                            <Select
                                v-model="selectedCategory"
                                :options="categoryOptions"
                                placeholder="All"
                                class="w-full sm:w-32"
                            />
                        </template>

                        <div class="grid grid-cols-2 gap-2 md:grid-cols-4">
                            <button
                                v-for="item in filteredItems"
                                :key="item.id"
                                type="button"
                                class="rounded border border-gray-200 p-3 text-left hover:bg-gray-50"
                                @click="addItem(item)"
                            >
                                <p class="truncate text-sm font-medium text-gray-900">{{ item.name }}</p>
                                <p class="text-xs text-gray-500">MVR {{ item.price }}</p>
                            </button>
                        </div>
                    </Card>

                    <Card v-if="form.order.items.length" title="Order Items">
                        <div class="space-y-2">
                            <div
                                v-for="(item, index) in form.order.items"
                                :key="`${item.item_id}-${index}`"
                                class="flex flex-col gap-2 border-b border-gray-100 py-2 last:border-0 sm:flex-row sm:items-center sm:justify-between"
                            >
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ item.name }}</p>
                                    <p class="text-xs text-gray-500">MVR {{ item.price }}</p>
                                </div>
                                <div class="flex flex-wrap items-center gap-3">
                                    <div class="flex items-center gap-1">
                                        <button type="button" class="flex h-7 w-7 items-center justify-center rounded border border-gray-300 text-sm" @click="updateQuantity(index, item.quantity - 1)">-</button>
                                        <span class="w-8 text-center text-sm">{{ item.quantity }}</span>
                                        <button type="button" class="flex h-7 w-7 items-center justify-center rounded border border-gray-300 text-sm" @click="updateQuantity(index, item.quantity + 1)">+</button>
                                    </div>
                                    <span class="w-20 text-right text-sm font-medium text-gray-900">MVR {{ (item.price * item.quantity).toLocaleString() }}</span>
                                    <button type="button" class="text-gray-400 hover:text-rose-600" @click="removeItem(index)">×</button>
                                </div>
                            </div>
                        </div>
                    </Card>
                </div>

                <div class="space-y-4">
                    <Card title="Options">
                        <div class="space-y-4">
                            <Select
                                v-model="form.order.status"
                                label="Status"
                                :options="[
                                    { value: 'pending', label: 'Pending' },
                                    { value: 'completed', label: 'Completed' },
                                    { value: 'cancelled', label: 'Cancelled' }
                                ]"
                            />
                            <Select
                                v-model="form.order.delivery_type"
                                label="Delivery"
                                :options="[
                                    { value: 'delivery', label: 'Delivery' },
                                    { value: 'pickup', label: 'Pickup' }
                                ]"
                            />
                            <Select
                                v-model="form.order.payment_method"
                                label="Payment"
                                :options="[
                                    { value: 'cash', label: 'Cash' },
                                    { value: 'transfer', label: 'Transfer' }
                                ]"
                            />
                            <Input
                                v-if="form.order.payment_method === 'transfer'"
                                v-model="form.order.transfer_reference_number"
                                label="Reference"
                                placeholder="Ref number"
                            />
                        </div>
                    </Card>

                    <Card title="Summary">
                        <div class="flex justify-between text-sm text-gray-500">
                            <span>Items</span>
                            <span>{{ totalItems }}</span>
                        </div>
                        <div class="mb-3 mt-2 flex justify-between text-sm text-gray-500">
                            <span>Quantity</span>
                            <span>{{ totalQuantity }}</span>
                        </div>
                        <div class="flex justify-between border-t border-gray-200 pt-3">
                            <span class="font-medium text-gray-900">Total</span>
                            <span class="text-lg font-semibold text-gray-900">MVR {{ totalAmount.toLocaleString() }}</span>
                        </div>
                        <Button type="submit" class="mt-4 w-full" :disabled="form.processing || !form.order.items.length">
                            {{ form.processing ? 'Saving...' : 'Save Changes' }}
                        </Button>
                    </Card>
                </div>
            </div>
        </form>
    </AppLayout>
</template>
