<script setup>
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import Input from '@/Components/Input.vue';
import Modal from '@/Components/Modal.vue';
import Select from '@/Components/Select.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    items: {
        type: Array,
        default: () => [],
    },
    categories: {
        type: Array,
        default: () => [],
    },
});

const isModalOpen = ref(false);
const editingItem = ref(null);
const filterCategory = ref('');

const form = useForm({
    name: '',
    price: '',
    category_id: '',
});

const categoryOptions = computed(() =>
    props.categories.map((cat) => ({ value: String(cat.id), label: cat.name })),
);

const filteredItems = computed(() => {
    if (!filterCategory.value) {
        return props.items;
    }

    return props.items.filter((item) => item.category_id === Number(filterCategory.value));
});

const openCreateModal = () => {
    form.reset();
    editingItem.value = null;
    isModalOpen.value = true;
};

const openEditModal = (item) => {
    form.name = item.name;
    form.price = String(item.price);
    form.category_id = item.category_id ? String(item.category_id) : '';
    editingItem.value = item;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingItem.value = null;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingItem.value) {
        form.put(`/items/${editingItem.value.id}`, { onSuccess: closeModal });
        return;
    }

    form.post('/items', { onSuccess: closeModal });
};

const handleDelete = (item) => {
    if (window.confirm('Delete this item?')) {
        router.delete(`/items/${item.id}`);
    }
};
</script>

<template>
    <AppLayout title="Items">
        <Head title="Items" />

        <Card title="Items">
            <template #actions>
                <div class="flex flex-col gap-2 sm:flex-row sm:items-center">
                    <Select
                        v-model="filterCategory"
                        :options="categoryOptions"
                        placeholder="All"
                        class="w-full sm:w-40"
                    />
                    <Button @click="openCreateModal">Add</Button>
                </div>
            </template>

            <div v-if="filteredItems.length" class="overflow-x-auto">
                <table class="w-full text-sm text-slate-700">
                    <thead class="border-y border-emerald-100 bg-emerald-50/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Category</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Price</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100/70">
                        <tr v-for="item in filteredItems" :key="item.id" class="transition-colors duration-150 hover:bg-emerald-50/45">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ item.name }}</td>
                            <td class="px-4 py-3">
                                <Badge>{{ item.category?.name || 'None' }}</Badge>
                            </td>
                            <td class="px-4 py-3 text-right font-medium text-gray-900">MVR {{ item.price }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="sm" @click="openEditModal(item)">Edit</Button>
                                    <Button variant="ghost" size="sm" class="text-rose-600" @click="handleDelete(item)">Delete</Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="p-4 text-center text-gray-500">No items</div>
        </Card>

        <Modal :show="isModalOpen" :title="editingItem ? 'Edit Item' : 'Add Item'" @close="closeModal">
            <form @submit.prevent="submit">
                <div class="space-y-4">
                    <Input v-model="form.name" label="Name" placeholder="Item name" :error="form.errors.name" autofocus />
                    <Input v-model="form.price" label="Price" type="number" placeholder="0" :error="form.errors.price" />
                    <Select
                        v-model="form.category_id"
                        label="Category"
                        :options="categoryOptions"
                        placeholder="Select"
                        :error="form.errors.category_id"
                    />
                </div>

                <div class="mt-4 flex justify-end gap-2 border-t border-gray-200 pt-4">
                    <Button type="button" variant="secondary" @click="closeModal">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save' }}
                    </Button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
