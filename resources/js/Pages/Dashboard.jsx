import Badge from '@/Components/Badge';
import Card from '@/Components/Card';
import StatCard from '@/Components/StatCard';
import Table from '@/Components/Table';
import AppLayout from '@/Layouts/AppLayout';
import { Head, Link } from '@inertiajs/react';

function ClipboardIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z" />
        </svg>
    );
}

function ClockIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
    );
}

function CurrencyIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M12 6v12m-3-2.818.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
        </svg>
    );
}

function UsersIcon({ className }) {
    return (
        <svg className={className} fill="none" viewBox="0 0 24 24" strokeWidth={1.5} stroke="currentColor">
            <path strokeLinecap="round" strokeLinejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
        </svg>
    );
}

export default function Dashboard({ stats = {}, recentOrders = [] }) {
    return (
        <AppLayout title="Dashboard">
            <Head title="Dashboard" />

            {/* Stats */}
            <div className="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-6">
                <StatCard
                    title="Total Orders"
                    value={stats.totalOrders || 0}
                    icon={ClipboardIcon}
                />
                <StatCard
                    title="Pending"
                    value={stats.pendingOrders || 0}
                    icon={ClockIcon}
                />
                <StatCard
                    title="Revenue"
                    value={`MVR ${(stats.totalRevenue || 0).toLocaleString()}`}
                    icon={CurrencyIcon}
                />
                <StatCard
                    title="Customers"
                    value={stats.totalCustomers || 0}
                    icon={UsersIcon}
                />
            </div>

            {/* Recent Orders */}
            <Card>
                <Card.Header>
                    <Card.Title>Recent Orders</Card.Title>
                    <Link href="/orders" className="text-sm text-gray-500 hover:text-gray-700">
                        View all →
                    </Link>
                </Card.Header>
                {recentOrders.length > 0 ? (
                    <Table>
                        <Table.Head>
                            <Table.HeadCell>Order</Table.HeadCell>
                            <Table.HeadCell>Customer</Table.HeadCell>
                            <Table.HeadCell>Status</Table.HeadCell>
                            <Table.HeadCell className="text-right">Amount</Table.HeadCell>
                        </Table.Head>
                        <Table.Body>
                            {recentOrders.map((order) => (
                                <Table.Row key={order.id}>
                                    <Table.Cell>
                                        <Link href={`/orders/${order.id}`} className="font-medium text-gray-900 hover:underline">
                                            #{order.order_number}
                                        </Link>
                                    </Table.Cell>
                                    <Table.Cell>{order.customer?.phone_number}</Table.Cell>
                                    <Table.Cell>
                                        <Badge.Status status={order.status} />
                                    </Table.Cell>
                                    <Table.Cell className="text-right font-medium text-gray-900">
                                        MVR {order.total_amount?.toLocaleString()}
                                    </Table.Cell>
                                </Table.Row>
                            ))}
                        </Table.Body>
                    </Table>
                ) : (
                    <div className="p-8 text-center text-gray-500">
                        No recent orders
                    </div>
                )}
            </Card>
        </AppLayout>
    );
}
