<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Customer;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'totalOrders' => Order::count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'totalRevenue' => Order::where('status', 'completed')->sum('total_amount'),
            'totalCustomers' => Customer::count(),
        ];

        $recentOrders = Order::with(['customer', 'items'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return Inertia::render('Dashboard', [
            'stats' => $stats,
            'recentOrders' => $recentOrders,
        ]);
    }
}
