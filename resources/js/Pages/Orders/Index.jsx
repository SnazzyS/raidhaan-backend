import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import Card from '@/Components/Card';
import Select from '@/Components/Select';
import Table from '@/Components/Table';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link } from '@inertiajs/react';
import { useState } from 'react';

export default function OrdersIndex({ orders = [] }) {
    const [statusFilter, setStatusFilter] = useState('active');

    const filteredOrders = orders.filter(order => {
        if (statusFilter === 'active') return !['completed', 'cancelled'].includes(order.status);
        if (statusFilter === 'all') return true;
        return order.status === statusFilter;
    });

    return (
        <AppLayout title="Orders">
            <Head title="Orders" />

            <Card>
                <Card.Header>
                    <div className="flex flex-col gap-2 sm:flex-row sm:items-center sm:gap-3">
                        <Card.Title>All Orders</Card.Title>
                        <Select
                            options={[
                                { value: 'active', label: 'Active Orders' },
                                { value: 'all', label: 'All Orders' },
                                { value: 'pending', label: 'Pending' },
                                { value: 'completed', label: 'Completed' },
                                { value: 'cancelled', label: 'Cancelled' },
                            ]}
                            placeholder="Filter by status"
                            value={statusFilter}
                            onChange={(e) => setStatusFilter(e.target.value)}
                            className="w-full sm:w-40"
                        />
                    </div>
                    <Link href="/orders/create">
                        <Button>New Order</Button>
                    </Link>
                </Card.Header>

                {filteredOrders.length > 0 ? (
                    <Table>
                        <Table.Head>
                            <Table.HeadCell>Order</Table.HeadCell>
                            <Table.HeadCell>Customer</Table.HeadCell>
                            <Table.HeadCell>Items</Table.HeadCell>
                            <Table.HeadCell>Status</Table.HeadCell>
                            <Table.HeadCell>Payment</Table.HeadCell>
                            <Table.HeadCell className="text-right">Amount</Table.HeadCell>
                            <Table.HeadCell></Table.HeadCell>
                        </Table.Head>
                        <Table.Body>
                            {filteredOrders.map((order) => (
                                <Table.Row key={order.id}>
                                    <Table.Cell>
                                        <Link href={`/orders/${order.id}`} className="font-medium text-gray-900 hover:underline">
                                            #{order.order_number}
                                        </Link>
                                    </Table.Cell>
                                    <Table.Cell>{order.customer?.phone_number}</Table.Cell>
                                    <Table.Cell>{order.items?.length || 0}</Table.Cell>
                                    <Table.Cell>
                                        <Badge.Status status={order.status} />
                                    </Table.Cell>
                                    <Table.Cell className="capitalize">{order.payment_method}</Table.Cell>
                                    <Table.Cell className="text-right font-medium text-gray-900">
                                        MVR {order.total_amount?.toLocaleString()}
                                    </Table.Cell>
                                    <Table.Cell>
                                        <div className="flex justify-end gap-2">
                                            <Link href={`/orders/${order.id}/edit`}>
                                                <Button variant="ghost" size="sm">Edit</Button>
                                            </Link>
                                        </div>
                                    </Table.Cell>
                                </Table.Row>
                            ))}
                        </Table.Body>
                    </Table>
                ) : (
                    <div className="p-8 text-center text-gray-500">
                        No orders found
                    </div>
                )}
            </Card>
        </AppLayout>
    );
}
