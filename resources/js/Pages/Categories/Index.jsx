import Button from '@/Components/Button';
import Card from '@/Components/Card';
import Input from '@/Components/Input';
import Modal from '@/Components/Modal';
import Table from '@/Components/Table';
import AppLayout from '@/Layouts/AppLayout';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function CategoriesIndex({ categories = [] }) {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingCategory, setEditingCategory] = useState(null);

    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
    });

    const openCreateModal = () => {
        reset();
        setEditingCategory(null);
        setIsModalOpen(true);
    };

    const openEditModal = (category) => {
        setData('name', category.name);
        setEditingCategory(category);
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setEditingCategory(null);
        reset();
    };

    const submit = (e) => {
        e.preventDefault();
        if (editingCategory) {
            put(`/categories/${editingCategory.id}`, { onSuccess: closeModal });
        } else {
            post('/categories', { onSuccess: closeModal });
        }
    };

    const handleDelete = (category) => {
        if (confirm('Delete this category?')) {
            router.delete(`/categories/${category.id}`);
        }
    };

    return (
        <AppLayout title="Categories">
            <Head title="Categories" />

            <Card>
                <Card.Header>
                    <Card.Title>Categories</Card.Title>
                    <Button onClick={openCreateModal}>Add</Button>
                </Card.Header>

                {categories.length > 0 ? (
                    <Table>
                        <Table.Head>
                            <Table.HeadCell>Name</Table.HeadCell>
                            <Table.HeadCell>Items</Table.HeadCell>
                            <Table.HeadCell></Table.HeadCell>
                        </Table.Head>
                        <Table.Body>
                            {categories.map((category) => (
                                <Table.Row key={category.id}>
                                    <Table.Cell className="font-medium text-gray-900">{category.name}</Table.Cell>
                                    <Table.Cell>{category.items_count || 0}</Table.Cell>
                                    <Table.Cell>
                                        <div className="flex justify-end gap-2">
                                            <Button variant="ghost" size="sm" onClick={() => openEditModal(category)}>Edit</Button>
                                            <Button variant="ghost" size="sm" className="text-rose-600" onClick={() => handleDelete(category)}>Delete</Button>
                                        </div>
                                    </Table.Cell>
                                </Table.Row>
                            ))}
                        </Table.Body>
                    </Table>
                ) : (
                    <div className="p-8 text-center text-gray-500">No categories</div>
                )}
            </Card>

            <Modal isOpen={isModalOpen} onClose={closeModal} title={editingCategory ? 'Edit Category' : 'Add Category'}>
                <form onSubmit={submit}>
                    <Input
                        label="Name"
                        placeholder="Category name"
                        value={data.name}
                        onChange={(e) => setData('name', e.target.value)}
                        error={errors.name}
                        autoFocus
                    />
                    <Modal.Footer>
                        <Button variant="secondary" type="button" onClick={closeModal}>Cancel</Button>
                        <Button type="submit" disabled={processing}>
                            {processing ? 'Saving...' : 'Save'}
                        </Button>
                    </Modal.Footer>
                </form>
            </Modal>
        </AppLayout>
    );
}
