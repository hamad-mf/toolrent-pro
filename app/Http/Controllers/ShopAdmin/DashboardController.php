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
        $reserved = Tool::where('status', 'Reserved')->count();
        $rented = Tool::where('status', 'Rented')->count();
        $maintenance = Tool::where('status', 'Maintenance')->count();
        $user = auth()->user();
        $tenant = $user->tenant;
        $canViewRentals = $user->hasRole(['shop-admin', 'manager', 'counter-staff'])
            && $tenant
            && $tenant->hasFeature('rentals');

        $stats = [
            'total_tools' => Tool::count(),
            'available_tools' => $available,
            'reserved_tools' => $reserved,
            'rented_tools' => $rented,
            'total_categories' => Category::count(),
        ];

        $chartData = [
            'labels' => ['Available', 'Reserved', 'Rented Out', 'In Maintenance'],
            'data' => [$available, $reserved, $rented, $maintenance]
        ];

        $recentRentals = $canViewRentals
            ? Rental::with(['customer', 'tool'])->latest()->take(5)->get()
            : collect();

        return view('shop-admin.dashboard', compact('stats', 'chartData', 'recentRentals', 'canViewRentals'));
    }
}
