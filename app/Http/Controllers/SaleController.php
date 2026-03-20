<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index(Request $request)
    {
        $query = $this->applyFilters(Sale::query(), $request);

        $sales = $query->orderByDesc('completed_at')->orderByDesc('created_at')->get();

        return response()->json($sales);
    }

    public function webIndex(Request $request)
    {
        $sales = $this->applyFilters(Sale::query(), $request)
            ->orderByDesc('completed_at')
            ->orderByDesc('created_at')
            ->get();

        $stats = [
            'count' => $sales->count(),
            'grossTotal' => $sales->sum('subtotal'),
            'discountTotal' => $sales->sum('discount_amount'),
            'total' => $sales->sum('total'),
            'cashTotal' => $sales->where('payment_method', 'cash')->sum('total'),
            'cardTotal' => $sales->where('payment_method', 'card')->sum('total'),
            'transferTotal' => $sales->where('payment_method', 'transfer')->sum('total'),
            'dineInCount' => $sales->where('delivery_type', 'dine_in')->count(),
            'deliveryCount' => $sales->where('delivery_type', 'delivery')->count(),
            'pickupCount' => $sales->where('delivery_type', 'pickup')->count(),
        ];

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'stats' => $stats,
            'filters' => [
                'from' => $request->from,
                'to' => $request->to,
                'payment_method' => $request->payment_method,
                'delivery_type' => $request->delivery_type,
            ],
        ]);
    }

    private function applyFilters($query, Request $request)
    {
        if ($request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $query->whereBetween('completed_at', [$from, $to]);
        } else {
            $query->whereDate('completed_at', today());
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('delivery_type')) {
            $query->where('delivery_type', $request->delivery_type);
        }

        return $query;
    }
}
