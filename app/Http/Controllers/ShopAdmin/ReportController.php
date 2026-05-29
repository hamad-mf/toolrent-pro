<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Rental;
use App\Models\Tool;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReportController extends Controller
{
    public function index()
    {
        $revenue = Rental::where('status', 'Returned')->sum('total_price');
        $totalRentals = Rental::count();
        $totalTools = Tool::count();
        $inUseTools = Tool::whereIn('status', ['Reserved', 'Rented'])->count();
        $utilizationRate = $totalTools > 0 ? round(($inUseTools / $totalTools) * 100, 1) : 0;
        
        $topTools = Tool::withCount('rentals')
            ->orderBy('rentals_count', 'desc')
            ->take(5)
            ->get();

        $overdueRentals = Rental::with(['customer', 'tool'])
            ->whereIn('status', ['Active', 'Overdue'])
            ->where('due_at', '<', now())
            ->latest('due_at')
            ->take(5)
            ->get();

        $recentRevenue = Rental::where('status', 'Returned')
            ->where('returned_at', '>=', now()->subDays(30))
            ->select(DB::raw('DATE(returned_at) as date'), DB::raw('SUM(total_price) as total'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        return view('shop-admin.reports.index', compact(
            'revenue',
            'totalRentals',
            'topTools',
            'recentRevenue',
            'overdueRentals',
            'utilizationRate'
        ));
    }
}
