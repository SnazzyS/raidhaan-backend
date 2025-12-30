import Badge from '@/Components/Badge';
import Button from '@/Components/Button';
import Card from '@/Components/Card';
import Modal from '@/Components/Modal';
import Table from '@/Components/Table';
import AppLayout from '@/Layouts/AppLayout';
import { printReceiptHtml } from '@/lib/qz';
import { Head, Link, router } from '@inertiajs/react';
import axios from 'axios';
import { useState } from 'react';

export default function OrderShow({ order }) {
    const [isConfirmOpen, setIsConfirmOpen] = useState(false);
    const [pendingStatus, setPendingStatus] = useState(null);
    const [isPrinting, setIsPrinting] = useState(false);

    const handleStatusChange = (newStatus) => {
        router.patch(`/orders/${order.id}/status`, {
            status: newStatus,
        }, {
            preserveScroll: true,
        });
    };

    const requestStatusChange = (newStatus) => {
        setPendingStatus(newStatus);
        setIsConfirmOpen(true);
    };

    const closeConfirm = () => {
        setIsConfirmOpen(false);
        setPendingStatus(null);
    };

    const confirmStatusChange = () => {
        if (!pendingStatus) return;
        handleStatusChange(pendingStatus);
        closeConfirm();
    };

    const handleQzPrint = async () => {
        setIsPrinting(true);
        try {
            const response = await axios.get(`/api/orders/${order.id}/receipt`, {
                params: { qz: 1 },
                responseType: 'text',
            });
            await printReceiptHtml(response.data);
        } catch (error) {
            console.error('QZ Tray print failed', error);
            const msg = error.message || 'Unknown error';
            window.alert(`Unable to print via QZ Tray.\nError: ${msg}\n\nMake sure QZ Tray is running and allowed.`);
        } finally {
            setIsPrinting(false);
        }
    };

    const confirmTitle = pendingStatus === 'cancelled'
        ? 'Cancel this order?'
        : 'Mark order as completed?';
    const confirmMessage = pendingStatus === 'cancelled'
        ? 'This will set the order status to Cancelled.'
        : 'This will set the order status to Completed.';

    return (
        <AppLayout title={`Order #${order.order_number}`}>
            <Head title={`Order #${order.order_number}`} />

            {/* Header Actions */}
            <div className="flex flex-col gap-3 mb-4 sm:flex-row sm:items-center sm:justify-between">
                <Link href="/orders">
                    <Button variant="ghost">← Back</Button>
                </Link>
                <div className="flex flex-wrap items-center gap-2">
                    <Button variant="secondary" onClick={handleQzPrint} disabled={isPrinting}>
                        {isPrinting ? 'Printing...' : 'Print (QZ)'}
                    </Button>
                    <a href={`/api/orders/${order.id}/receipt`} target="_blank" rel="noopener noreferrer">
                        <Button variant="ghost">Browser Print</Button>
                    </a>
                    <Link href={`/orders/${order.id}/edit`}>
                        <Button>Edit</Button>
                    </Link>
                </div>
            </div>

            <div className="grid grid-cols-1 lg:grid-cols-3 gap-4">
                {/* Order Items */}
                <div className="lg:col-span-2">
                    <Card>
                        <Card.Header>
                            <Card.Title>Items</Card.Title>
                        </Card.Header>
                        <Table>
                            <Table.Head>
                                <Table.HeadCell>Item</Table.HeadCell>
                                <Table.HeadCell className="text-center">Qty</Table.HeadCell>
                                <Table.HeadCell className="text-right">Price</Table.HeadCell>
                                <Table.HeadCell className="text-right">Total</Table.HeadCell>
                            </Table.Head>
                            <Table.Body>
                                {order.items?.map((item) => (
                                    <Table.Row key={item.id}>
                                        <Table.Cell className="font-medium text-gray-900">{item.name}</Table.Cell>
                                        <Table.Cell className="text-center">{item.pivot.quantity}</Table.Cell>
                                        <Table.Cell className="text-right">MVR {item.pivot.price?.toLocaleString()}</Table.Cell>
                                        <Table.Cell className="text-right font-medium text-gray-900">
                                            MVR {(item.pivot.quantity * item.pivot.price).toLocaleString()}
                                        </Table.Cell>
                                    </Table.Row>
                                ))}
                            </Table.Body>
                        </Table>
                        <div className="px-4 py-3 border-t border-gray-200 flex justify-between">
                            <span className="font-medium text-gray-900">Total</span>
                            <span className="text-lg font-semibold text-gray-900">MVR {order.total_amount?.toLocaleString()}</span>
                        </div>
                    </Card>
                </div>

                {/* Sidebar */}
                <div className="space-y-4">
                    {/* Status */}
                    <Card>
                        <Card.Header>
                            <Card.Title>Status</Card.Title>
                        </Card.Header>
                        <div className="p-4 space-y-3">
                            <div className="flex items-center justify-between">
                                <span className="text-sm text-gray-500">Current</span>
                                <Badge.Status status={order.status} />
                            </div>
                            {order.status === 'pending' && (
                                <div className="flex gap-2">
                                    <Button
                                        variant="success"
                                        size="sm"
                                        className="flex-1"
                                        onClick={() => requestStatusChange('completed')}
                                    >
                                        Complete
                                    </Button>
                                    <Button
                                        variant="danger"
                                        size="sm"
                                        className="flex-1"
                                        onClick={() => requestStatusChange('cancelled')}
                                    >
                                        Cancel
                                    </Button>
                                </div>
                            )}
                        </div>
                    </Card>

                    {/* Customer */}
                    <Card>
                        <Card.Header>
                            <Card.Title>Customer</Card.Title>
                        </Card.Header>
                        <div className="p-4 space-y-2 text-sm">
                            <div>
                                <span className="text-gray-500">Phone: </span>
                                <span className="text-gray-900">{order.customer?.phone_number}</span>
                            </div>
                            <div>
                                <span className="text-gray-500">Address: </span>
                                <span className="text-gray-900">{order.customer?.address}</span>
                            </div>
                            <div>
                                <span className="text-gray-500">City: </span>
                                <span className="text-gray-900 capitalize">{order.customer?.city}</span>
                            </div>
                        </div>
                    </Card>

                    {/* Order Info */}
                    <Card>
                        <Card.Header>
                            <Card.Title>Info</Card.Title>
                        </Card.Header>
                        <div className="p-4 space-y-2 text-sm">
                            <div className="flex justify-between">
                                <span className="text-gray-500">Delivery</span>
                                <span className="text-gray-900 capitalize">{order.delivery_type}</span>
                            </div>
                            <div className="flex justify-between">
                                <span className="text-gray-500">Payment</span>
                                <span className="text-gray-900 capitalize">{order.payment_method}</span>
                            </div>
                            {order.transfer_reference_number && (
                                <div className="flex justify-between">
                                    <span className="text-gray-500">Reference</span>
                                    <span className="text-gray-900 font-mono">{order.transfer_reference_number}</span>
                                </div>
                            )}
                            <div className="flex justify-between">
                                <span className="text-gray-500">Created</span>
                                <span className="text-gray-900">{new Date(order.created_at).toLocaleDateString()}</span>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>

            <Modal isOpen={isConfirmOpen} onClose={closeConfirm} title={confirmTitle}>
                <p className="text-sm text-gray-600">{confirmMessage}</p>
                <Modal.Footer>
                    <Button variant="secondary" onClick={closeConfirm}>No, go back</Button>
                    <Button
                        variant={pendingStatus === 'cancelled' ? 'danger' : 'success'}
                        onClick={confirmStatusChange}
                    >
                        Yes, confirm
                    </Button>
                </Modal.Footer>
            </Modal>
        </AppLayout>
    );
}
