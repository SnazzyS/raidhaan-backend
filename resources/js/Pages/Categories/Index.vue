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

        <Card title="Categories" description="Create and maintain the groups used to organize sale items.">
            <template #actions>
                <Button @click="openCreateModal">Add Category</Button>
            </template>

            <div v-if="props.categories.length" class="-mx-5 -mb-5 overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                    <thead class="bg-slate-50">
                        <tr>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Name</th>
                            <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Items</th>
                            <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 bg-white">
                        <tr v-for="category in props.categories" :key="category.id" class="hover:bg-slate-50">
                            <td class="px-5 py-4 font-medium text-slate-900">{{ category.name }}</td>
                            <td class="px-5 py-4">{{ category.items_count || 0 }}</td>
                            <td class="px-5 py-4">
                                <div class="flex justify-end gap-2">
                                    <Button variant="ghost" size="sm" @click="openEditModal(category)">Edit</Button>
                                    <Button variant="ghost" size="sm" class="text-rose-600 hover:bg-rose-50 hover:text-rose-700" @click="handleDelete(category)">Delete</Button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div
                v-else
                class="rounded-lg border border-dashed border-slate-300 bg-slate-50 px-6 py-12 text-center text-sm text-slate-500"
            >
                No categories created yet.
            </div>
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

                <div class="mt-5 flex justify-end gap-2 border-t border-slate-200 pt-4">
                    <Button type="button" variant="secondary" @click="closeModal">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save' }}
                    </Button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
