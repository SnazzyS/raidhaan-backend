<?php

namespace App\Http\Controllers;

use App\Actions\Dashboard\GetDashboardData;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(GetDashboardData $getDashboardData)
    {
        return Inertia::render('Dashboard', $getDashboardData->handle());
    }
}
