import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import Card from '@/Components/Card';
import Input from '@/Components/Input';
import StatCard from '@/Components/StatCard';
import Table from '@/Components/Table';
import AppLayout from '@/Layouts/AppLayout';
import { Head, router } from '@inertiajs/react';
import { useState } from 'react';

export default function SalesIndex({ sales = [], stats = {}, filters = {} }) {
    const [fromDate, setFromDate] = useState(filters.from || '');
    const [toDate, setToDate] = useState(filters.to || '');

    const handleFilter = () => {
        router.get('/sales', { from: fromDate, to: toDate }, { preserveState: true, preserveScroll: true });
    };

    const handleClear = () => {
        setFromDate('');
        setToDate('');
        router.get('/sales', {}, { preserveState: true });
    };

    return (
        <AppLayout title="Sales">
            <Head title="Sales" />

            <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-4">
                <StatCard title="Sales" value={stats.count || 0} />
                <StatCard title="Revenue" value={`MVR ${(stats.total || 0).toLocaleString()}`} />
                <StatCard title="Cash" value={`MVR ${(stats.cashTotal || 0).toLocaleString()}`} />
            </div>

            <Card>
                <Card.Header>
                    <Card.Title>Sales History</Card.Title>
                    <div className="flex flex-wrap items-center gap-2">
                        <Input type="date" value={fromDate} onChange={(e) => setFromDate(e.target.value)} className="w-full sm:w-36" />
                        <span className="hidden text-gray-400 sm:inline">to</span>
                        <Input type="date" value={toDate} onChange={(e) => setToDate(e.target.value)} className="w-full sm:w-36" />
                        <Button onClick={handleFilter} className="w-full sm:w-auto">Filter</Button>
                        {(fromDate || toDate) && (
                            <Button variant="ghost" onClick={handleClear} className="w-full sm:w-auto">Clear</Button>
                        )}
                    </div>
                </Card.Header>

                {sales.length > 0 ? (
                    <Table>
                        <Table.Head>
                            <Table.HeadCell>Order</Table.HeadCell>
                            <Table.HeadCell>Date</Table.HeadCell>
                            <Table.HeadCell>Payment</Table.HeadCell>
                            <Table.HeadCell className="text-right">Amount</Table.HeadCell>
                        </Table.Head>
                        <Table.Body>
                            {sales.map((sale) => (
                                <Table.Row key={sale.id}>
                                    <Table.Cell className="font-medium text-gray-900">#{sale.order_number}</Table.Cell>
                                    <Table.Cell>{new Date(sale.created_at).toLocaleDateString()}</Table.Cell>
                                    <Table.Cell>
                                        <Badge variant={sale.payment_method === 'cash' ? 'success' : 'primary'}>{sale.payment_method}</Badge>
                                    </Table.Cell>
                                    <Table.Cell className="text-right font-medium text-gray-900">MVR {sale.total?.toLocaleString() || 0}</Table.Cell>
                                </Table.Row>
                            ))}
                        </Table.Body>
                    </Table>
                ) : (
                    <div className="p-8 text-center text-gray-500">No sales found</div>
                )}
            </Card>
        </AppLayout>
    );
}
