<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\AllowedFilter;

class SaleController extends Controller
{
    public function index()
    {

        $sales = QueryBuilder::for(Sale::class)
        ->AllowedFilters([
            AllowedFilter::exact('date'),
        ])->get();
        
        return response()->json($sales);
    }
}
