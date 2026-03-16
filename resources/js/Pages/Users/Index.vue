<script setup>
import Badge from '@/Components/Badge.vue';
import Button from '@/Components/Button.vue';
import Card from '@/Components/Card.vue';
import Input from '@/Components/Input.vue';
import Modal from '@/Components/Modal.vue';
import Select from '@/Components/Select.vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import { Head, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    users: {
        type: Array,
        default: () => [],
    },
});

const page = usePage();
const isModalOpen = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    role: 'staff',
    password: '',
    password_confirmation: '',
});

const roleOptions = [
    { value: 'staff', label: 'Staff' },
    { value: 'admin', label: 'Admin' },
];

const currentUserId = computed(() => page.props.auth?.user?.id ?? null);
const adminCount = computed(() => props.users.filter((user) => user.role === 'admin').length);
const staffCount = computed(() => props.users.filter((user) => user.role === 'staff').length);

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    form.role = 'staff';
    isModalOpen.value = true;
};

const openEditModal = (user) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.password = '';
    form.password_confirmation = '';
    form.clearErrors();
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    editingUser.value = null;
    form.reset();
    form.clearErrors();
    form.role = 'staff';
};

const submit = () => {
    if (editingUser.value) {
        form.put(`/users/${editingUser.value.id}`, { onSuccess: closeModal });
        return;
    }

    form.post('/users', { onSuccess: closeModal });
};

const handleDelete = (user) => {
    if (!window.confirm(`Remove ${user.name}?`)) {
        return;
    }

    router.delete(`/users/${user.id}`);
};

const formatDate = (value) => new Intl.DateTimeFormat('en-GB', {
    day: '2-digit',
    month: 'short',
    year: 'numeric',
}).format(new Date(value));
</script>

<template>
    <AppLayout title="Users">
        <Head title="Users" />

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-[minmax(0,1fr)_320px]">
            <Card title="Team Accounts" description="Create accounts for admins and staff, then adjust access as the team changes.">
                <template #actions>
                    <Button @click="openCreateModal">Add User</Button>
                </template>

                <div v-if="props.users.length" class="-mx-5 -mb-5 overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm text-slate-700">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">User</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Role</th>
                                <th class="px-5 py-3 text-left text-xs font-medium uppercase tracking-wide text-slate-500">Added</th>
                                <th class="px-5 py-3 text-right text-xs font-medium uppercase tracking-wide text-slate-500">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 bg-white">
                            <tr v-for="user in props.users" :key="user.id" class="hover:bg-slate-50">
                                <td class="px-5 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-100 text-sm font-semibold text-slate-700">
                                            {{ user.name.slice(0, 1).toUpperCase() }}
                                        </div>
                                        <div>
                                            <div class="flex items-center gap-2">
                                                <p class="font-medium text-slate-900">{{ user.name }}</p>
                                                <Badge v-if="user.id === currentUserId" variant="info">You</Badge>
                                            </div>
                                            <p class="text-sm text-slate-500">{{ user.email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4">
                                    <Badge :variant="user.role === 'admin' ? 'primary' : 'default'">
                                        {{ user.role === 'admin' ? 'Admin' : 'Staff' }}
                                    </Badge>
                                </td>
                                <td class="px-5 py-4 text-slate-500">{{ formatDate(user.created_at) }}</td>
                                <td class="px-5 py-4">
                                    <div class="flex justify-end gap-2">
                                        <Button variant="ghost" size="sm" @click="openEditModal(user)">Edit</Button>
                                        <Button
                                            variant="ghost"
                                            size="sm"
                                            class="text-rose-600 hover:bg-rose-50 hover:text-rose-700"
                                            :disabled="user.id === currentUserId"
                                            @click="handleDelete(user)"
                                        >
                                            Remove
                                        </Button>
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
                    No users created yet.
                </div>
            </Card>

            <Card title="Access Summary" description="Use admin accounts for configuration and staff accounts for day-to-day operation.">
                <div class="space-y-4 text-sm">
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Total users</span>
                        <span class="font-semibold text-slate-900">{{ props.users.length }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Admins</span>
                        <span class="font-semibold text-slate-900">{{ adminCount }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Staff</span>
                        <span class="font-semibold text-slate-900">{{ staffCount }}</span>
                    </div>
                </div>

                <div class="mt-5 rounded-lg border border-slate-200 bg-slate-50 px-4 py-3 text-sm text-slate-600">
                    Keep at least one admin account active so restaurant settings, sales, users, items, and categories remain manageable.
                </div>
            </Card>
        </div>

        <Modal :show="isModalOpen" :title="editingUser ? 'Edit User' : 'Add User'" @close="closeModal">
            <form @submit.prevent="submit">
                <div class="space-y-4">
                    <Input v-model="form.name" label="Name" placeholder="Full name" :error="form.errors.name" autofocus />
                    <Input v-model="form.email" label="Email" type="email" placeholder="name@example.com" :error="form.errors.email" />
                    <Select v-model="form.role" label="Role" :options="roleOptions" :error="form.errors.role" />
                    <Input
                        v-model="form.password"
                        :label="editingUser ? 'New Password (optional)' : 'Password'"
                        type="password"
                        placeholder="Minimum 8 characters"
                        :error="form.errors.password"
                    />
                    <Input
                        v-model="form.password_confirmation"
                        :label="editingUser ? 'Confirm New Password' : 'Confirm Password'"
                        type="password"
                        placeholder="Repeat password"
                    />
                </div>

                <div class="mt-5 flex justify-end gap-2 border-t border-slate-200 pt-4">
                    <Button type="button" variant="secondary" @click="closeModal">Cancel</Button>
                    <Button type="submit" :disabled="form.processing">
                        {{ form.processing ? 'Saving...' : 'Save User' }}
                    </Button>
                </div>
            </form>
        </Modal>
    </AppLayout>
</template>
