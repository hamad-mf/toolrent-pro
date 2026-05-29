<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Tenant;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_tenants' => Tenant::count(),
            'total_users' => User::count(),
            'active_tenants' => Tenant::where('is_active', true)->count(),
        ];

        $tenantGrowth = Tenant::select(DB::raw('DATE_FORMAT(created_at, "%M %Y") as month'), DB::raw('count(*) as total'))
            ->groupBy('month')
            ->orderBy('created_at')
            ->get();

        $planStats = Tenant::select('plan', DB::raw('count(*) as total'))
            ->groupBy('plan')
            ->get();

        return view('super-admin.dashboard', compact('stats', 'tenantGrowth', 'planStats'));
    }
}
