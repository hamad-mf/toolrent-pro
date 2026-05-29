<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = auth()->user();

        if ($user->isSuperAdmin()) {
            return redirect()->route('super-admin.dashboard');
        }

        if ($user->isShopAdmin() || $user->hasRole([\App\Models\Role::MANAGER, \App\Models\Role::COUNTER_STAFF, \App\Models\Role::FLOOR_STAFF])) {
            return redirect()->route('shop-admin.dashboard');
        }

        return view('home');
    }
}
