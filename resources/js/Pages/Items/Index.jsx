import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import Card from '@/Components/Card';
import Input from '@/Components/Input';
import Modal from '@/Components/Modal';
import Select from '@/Components/Select';
import Table from '@/Components/Table';
import AppLayout from '@/Layouts/AppLayout';
import { Head, router, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function ItemsIndex({ items = [], categories = [] }) {
    const [isModalOpen, setIsModalOpen] = useState(false);
    const [editingItem, setEditingItem] = useState(null);
    const [filterCategory, setFilterCategory] = useState('');

    const { data, setData, post, put, processing, errors, reset } = useForm({
        name: '',
        price: '',
        category_id: '',
    });

    const filteredItems = filterCategory
        ? items.filter(item => item.category_id === parseInt(filterCategory))
        : items;

    const openCreateModal = () => {
        reset();
        setEditingItem(null);
        setIsModalOpen(true);
    };

    const openEditModal = (item) => {
        setData({
            name: item.name,
            price: item.price.toString(),
            category_id: item.category_id?.toString() || '',
        });
        setEditingItem(item);
        setIsModalOpen(true);
    };

    const closeModal = () => {
        setIsModalOpen(false);
        setEditingItem(null);
        reset();
    };

    const submit = (e) => {
        e.preventDefault();
        if (editingItem) {
            put(`/items/${editingItem.id}`, { onSuccess: closeModal });
        } else {
            post('/items', { onSuccess: closeModal });
        }
    };

    const handleDelete = (item) => {
        if (confirm('Delete this item?')) {
            router.delete(`/items/${item.id}`);
        }
    };

    return (
        <AppLayout title="Items">
            <Head title="Items" />
            <Card>
                <Card.Header>
                    <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                        <Card.Title>Items</Card.Title>
                        <Select options={categories.map(cat => ({ value: cat.id, label: cat.name }))} placeholder="All" value={filterCategory} onChange={(e) => setFilterCategory(e.target.value)} className="w-full sm:w-32" />
                    </div>
                    <Button onClick={openCreateModal}>Add</Button>
                </Card.Header>
                {filteredItems.length > 0 ? (
                    <Table>
                        <Table.Head>
                            <Table.HeadCell>Name</Table.HeadCell>
                            <Table.HeadCell>Category</Table.HeadCell>
                            <Table.HeadCell className="text-right">Price</Table.HeadCell>
                            <Table.HeadCell></Table.HeadCell>
                        </Table.Head>
                        <Table.Body>
                            {filteredItems.map((item) => (
                                <Table.Row key={item.id}>
                                    <Table.Cell className="font-medium text-gray-900">{item.name}</Table.Cell>
                                    <Table.Cell><Badge>{item.category?.name || 'None'}</Badge></Table.Cell>
                                    <Table.Cell className="text-right font-medium text-gray-900">MVR {item.price}</Table.Cell>
                                    <Table.Cell>
                                        <div className="flex justify-end gap-2">
                                            <Button variant="ghost" size="sm" onClick={() => openEditModal(item)}>Edit</Button>
                                            <Button variant="ghost" size="sm" className="text-rose-600" onClick={() => handleDelete(item)}>Delete</Button>
                                        </div>
                                    </Table.Cell>
                                </Table.Row>
                            ))}
                        </Table.Body>
                    </Table>
                ) : (<div className="p-8 text-center text-gray-500">No items</div>)}
            </Card>
            <Modal isOpen={isModalOpen} onClose={closeModal} title={editingItem ? 'Edit Item' : 'Add Item'}>
                <form onSubmit={submit}>
                    <div className="space-y-4">
                        <Input label="Name" placeholder="Item name" value={data.name} onChange={(e) => setData('name', e.target.value)} error={errors.name} autoFocus />
                        <Input label="Price" type="number" placeholder="0" value={data.price} onChange={(e) => setData('price', e.target.value)} error={errors.price} />
                        <Select label="Category" options={categories.map(cat => ({ value: cat.id, label: cat.name }))} placeholder="Select" value={data.category_id} onChange={(e) => setData('category_id', e.target.value)} error={errors.category_id} />
                    </div>
                    <Modal.Footer>
                        <Button variant="secondary" type="button" onClick={closeModal}>Cancel</Button>
                        <Button type="submit" disabled={processing}>{processing ? 'Saving...' : 'Save'}</Button>
                    </Modal.Footer>
                </form>
            </Modal>
        </AppLayout>
    );
}
