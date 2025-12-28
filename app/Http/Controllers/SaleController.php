<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Sale;
use Illuminate\Http\Request;
use Inertia\Inertia;

class SaleController extends Controller
{
    // API Method
    public function index(Request $request)
    {
        $query = Sale::query();

        if ($request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        } else {
            $query->whereDate('created_at', today());
        }

        $sales = $query->get();

        return response()->json($sales);
    }

    // Web/Inertia Method
    public function webIndex(Request $request)
    {
        $query = Sale::query();

        if ($request->filled('from') && $request->filled('to')) {
            $from = Carbon::parse($request->from)->startOfDay();
            $to = Carbon::parse($request->to)->endOfDay();
            $query->whereBetween('created_at', [$from, $to]);
        } else {
            $query->whereDate('created_at', today());
        }

        $sales = $query->orderBy('created_at', 'desc')->get();

        $stats = [
            'count' => $sales->count(),
            'total' => $sales->sum('total'),
            'cashTotal' => $sales->where('payment_method', 'cash')->sum('total'),
        ];

        return Inertia::render('Sales/Index', [
            'sales' => $sales,
            'stats' => $stats,
            'filters' => [
                'from' => $request->from,
                'to' => $request->to,
            ],
        ]);
    }
}
