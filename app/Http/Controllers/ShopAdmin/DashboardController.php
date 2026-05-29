<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\Category;
use App\Models\Rental;

class DashboardController extends Controller
{
    public function index()
    {
        $available = Tool::where('status', 'Available')->count();
        $rented = Tool::where('status', 'Rented')->count();
        $maintenance = Tool::where('status', 'Maintenance')->count();

        $stats = [
            'total_tools' => Tool::count(),
            'available_tools' => $available,
            'rented_tools' => $rented,
            'total_categories' => Category::count(),
        ];

        $chartData = [
            'labels' => ['Available', 'Rented Out', 'In Maintenance'],
            'data' => [$available, $rented, $maintenance]
        ];

        $recentRentals = Rental::with(['customer', 'tool'])->latest()->take(5)->get();

        return view('shop-admin.dashboard', compact('stats', 'chartData', 'recentRentals'));
    }
}
