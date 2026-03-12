<script setup>
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import Input from '@/Components/Input.vue';
import Modal from '@/Components/Modal.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    categories: {
        type: Array,
        default: () => [],
    },
});

const isModalOpen = ref(false);
const editingCategory = ref(null);

const form = useForm({
    name: '',
});

const openCreateModal = () => {
    form.reset();
    editingCategory.value = null;
    isModalOpen.value = true;
};

const openEditModal = (category) => {
    form.name = category.name;
    editingCategory.value = category;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingCategory.value = null;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingCategory.value) {
        form.put(`/categories/${editingCategory.value.id}`, { onSuccess: closeModal });
        return;
    }

    form.post('/categories', { onSuccess: closeModal });
};

const handleDelete = (category) => {
    if (window.confirm('Delete this category?')) {
        router.delete(`/categories/${category.id}`);
    }
};
</script>

<template>
    <AppLayout title="Categories">
        <Head title="Categories" />

        <Card title="Categories">
            <template #actions>
                <Button @click="openCreateModal">Add</Button>
            </template>

            <div v-if="props.categories.length" class="overflow-x-auto">
                <table class="w-full text-sm text-slate-700">
                    <thead class="border-y border-emerald-100 bg-emerald-50/60">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Name</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wider text-slate-500">Items</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold uppercase tracking-wider text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-emerald-100/70">
                        <tr v-for="category in props.categories" :key="category.id" class="transition-colors duration-150 hover:bg-emerald-50/45">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ category.name }}</td>
                            <td class="px-4 py-3">{{ category.items_count || 0 }}</td>
                            <td class="px-4 py-3">
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="sm" @click="openEditModal(category)">Edit</Button>
                                    <Button variant="ghost" size="sm" class="text-rose-600" @click="handleDelete(category)">Delete</Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div v-else class="p-4 text-center text-gray-500">No categories</div>
        </Card>

        <Modal :show="isModalOpen" :title="editingCategory ? 'Edit Category' : 'Add Category'" @close="closeModal">
            <form @submit.prevent="submit">
                <Input
                    v-model="form.name"
                    label="Name"
                    placeholder="Category name"
                    :error="form.errors.name"
                    autofocus
                />

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
