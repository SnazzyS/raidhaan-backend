import Button from '@/Components/Button';
import Card from '@/Components/Card';
import Input from '@/Components/Input';
import Select from '@/Components/Select';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function OrderCreate({ items = [], categories = [] }) {
    const { data, setData, post, processing, errors } = useForm({
        phone_number: '',
        address: '',
        city: '',
        order: {
            status: 'pending',
            delivery_type: 'delivery',
            payment_method: 'cash',
            transfer_reference_number: '',
            items: [],
        },
    });

    const [selectedCategory, setSelectedCategory] = useState('');

    const filteredItems = selectedCategory
        ? items.filter(item => item.category_id === parseInt(selectedCategory))
        : items;

    const addItem = (item) => {
        const existingIndex = data.order.items.findIndex(i => i.item_id === item.id);

        if (existingIndex > -1) {
            const newItems = [...data.order.items];
            newItems[existingIndex].quantity += 1;
            setData('order', { ...data.order, items: newItems });
        } else {
            setData('order', {
                ...data.order,
                items: [...data.order.items, { item_id: item.id, quantity: 1, name: item.name, price: item.price }],
            });
        }
    };

    const updateQuantity = (index, quantity) => {
        if (quantity < 1) return removeItem(index);
        const newItems = [...data.order.items];
        newItems[index].quantity = quantity;
        setData('order', { ...data.order, items: newItems });
    };

    const removeItem = (index) => {
        const newItems = data.order.items.filter((_, i) => i !== index);
        setData('order', { ...data.order, items: newItems });
    };

    const calculateTotal = () => {
        return data.order.items.reduce((total, item) => total + (item.price * item.quantity), 0);
    };

    const submit = (e) => {
        e.preventDefault();
        post('/orders');
    };

    return (
        <AppLayout title="Create Order">
            <Head title="Create Order" />

            <div className="mb-4">
                <Link href="/orders">
                    <Button variant="ghost">← Back</Button>
                </Link>
            </div>

            <form onSubmit={submit}>
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div className="lg:col-span-2 space-y-4">
                        {/* Customer */}
                        <Card>
                            <Card.Header>
                                <Card.Title>Customer</Card.Title>
                            </Card.Header>
                            <div className="p-4 grid grid-cols-1 md:grid-cols-3 gap-4">
                                <Input
                                    label="Phone"
                                    placeholder="7123456"
                                    value={data.phone_number}
                                    onChange={(e) => setData('phone_number', e.target.value)}
                                    error={errors.phone_number}
                                />
                                <Input
                                    label="Address"
                                    placeholder="House, street"
                                    value={data.address}
                                    onChange={(e) => setData('address', e.target.value)}
                                    error={errors.address}
                                />
                                <Select
                                    label="City"
                                    options={[
                                        { value: 'male', label: "Male'" },
                                        { value: 'hulhumale phase 1', label: 'Hulhumale Phase 1' },
                                        { value: 'hulhumale phase 2', label: 'Hulhumale Phase 2' },
                                    ]}
                                    placeholder="Select city"
                                    value={data.city}
                                    onChange={(e) => setData('city', e.target.value)}
                                    error={errors.city}
                                />
                            </div>
                        </Card>

                        {/* Items */}
                        <Card>
                            <Card.Header>
                                <Card.Title>Items</Card.Title>
                                <Select
                                    options={categories.map(cat => ({ value: cat.id, label: cat.name }))}
                                    placeholder="All"
                                    value={selectedCategory}
                                    onChange={(e) => setSelectedCategory(e.target.value)}
                                    className="w-full sm:w-32"
                                />
                            </Card.Header>
                            <div className="p-4 grid grid-cols-2 md:grid-cols-4 gap-2">
                                {filteredItems.map((item) => (
                                    <button
                                        key={item.id}
                                        type="button"
                                        onClick={() => addItem(item)}
                                        className="p-3 text-left border border-gray-200 rounded hover:bg-gray-50"
                                    >
                                        <p className="text-sm font-medium text-gray-900 truncate">{item.name}</p>
                                        <p className="text-xs text-gray-500">MVR {item.price}</p>
                                    </button>
                                ))}
                            </div>
                        </Card>

                        {/* Selected Items */}
                        {data.order.items.length > 0 && (
                            <Card>
                                <Card.Header>
                                    <Card.Title>Selected</Card.Title>
                                </Card.Header>
                                <div className="p-4 space-y-2">
                                    {data.order.items.map((item, index) => (
                                        <div key={index} className="flex flex-col gap-2 py-2 border-b border-gray-100 last:border-0 sm:flex-row sm:items-center sm:justify-between">
                                            <div>
                                                <p className="text-sm font-medium text-gray-900">{item.name}</p>
                                                <p className="text-xs text-gray-500">MVR {item.price}</p>
                                            </div>
                                            <div className="flex flex-wrap items-center gap-3">
                                                <div className="flex items-center gap-1">
                                                    <button
                                                        type="button"
                                                        onClick={() => updateQuantity(index, item.quantity - 1)}
                                                        className="w-7 h-7 flex items-center justify-center border border-gray-300 rounded text-sm"
                                                    >-</button>
                                                    <span className="w-8 text-center text-sm">{item.quantity}</span>
                                                    <button
                                                        type="button"
                                                        onClick={() => updateQuantity(index, item.quantity + 1)}
                                                        className="w-7 h-7 flex items-center justify-center border border-gray-300 rounded text-sm"
                                                    >+</button>
                                                </div>
                                                <span className="text-sm font-medium text-gray-900 w-20 text-right">
                                                    MVR {(item.price * item.quantity).toLocaleString()}
                                                </span>
                                                <button
                                                    type="button"
                                                    onClick={() => removeItem(index)}
                                                    className="text-gray-400 hover:text-rose-600"
                                                >×</button>
                                            </div>
                                        </div>
                                    ))}
                                </div>
                            </Card>
                        )}
                    </div>

                    {/* Sidebar */}
                    <div className="space-y-4">
                        <Card>
                            <Card.Header>
                                <Card.Title>Options</Card.Title>
                            </Card.Header>
                            <div className="p-4 space-y-4">
                                <Select
                                    label="Delivery"
                                    options={[
                                        { value: 'delivery', label: 'Delivery' },
                                        { value: 'pickup', label: 'Pickup' },
                                    ]}
                                    value={data.order.delivery_type}
                                    onChange={(e) => setData('order', { ...data.order, delivery_type: e.target.value })}
                                />
                                <Select
                                    label="Payment"
                                    options={[
                                        { value: 'cash', label: 'Cash' },
                                        { value: 'transfer', label: 'Transfer' },
                                    ]}
                                    value={data.order.payment_method}
                                    onChange={(e) => setData('order', { ...data.order, payment_method: e.target.value })}
                                />
                                {data.order.payment_method === 'transfer' && (
                                    <Input
                                        label="Reference"
                                        placeholder="Ref number"
                                        value={data.order.transfer_reference_number}
                                        onChange={(e) => setData('order', { ...data.order, transfer_reference_number: e.target.value })}
                                    />
                                )}
                            </div>
                        </Card>

                        <Card>
                            <Card.Header>
                                <Card.Title>Summary</Card.Title>
                            </Card.Header>
                            <div className="p-4">
                                <div className="flex justify-between text-sm text-gray-500 mb-2">
                                    <span>Items</span>
                                    <span>{data.order.items.length}</span>
                                </div>
                                <div className="flex justify-between text-sm text-gray-500 mb-3">
                                    <span>Quantity</span>
                                    <span>{data.order.items.reduce((sum, item) => sum + item.quantity, 0)}</span>
                                </div>
                                <div className="flex justify-between border-t border-gray-200 pt-3">
                                    <span className="font-medium text-gray-900">Total</span>
                                    <span className="text-lg font-semibold text-gray-900">MVR {calculateTotal().toLocaleString()}</span>
                                </div>
                                <Button type="submit" className="w-full mt-4" disabled={processing || data.order.items.length === 0}>
                                    {processing ? 'Creating...' : 'Create Order'}
                                </Button>
                            </div>
                        </Card>
                    </div>
                </div>
            </form>
        </AppLayout>
    );
}
