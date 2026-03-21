<?php

namespace App\Http\Controllers;

use App\Actions\Sales\GetSalesReport;
use App\Http\Requests\SaleFiltersRequest;
use Inertia\Inertia;

class SaleController extends Controller
{
    public function index(SaleFiltersRequest $request, GetSalesReport $getSalesReport)
    {
        return response()->json($getSalesReport->handle($request->validated())['sales']);
    }

    public function webIndex(SaleFiltersRequest $request, GetSalesReport $getSalesReport)
    {
        return Inertia::render('Sales/Index', $getSalesReport->handle($request->validated()));
    }
}
