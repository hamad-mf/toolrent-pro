<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::with('role')->latest()->paginate(10);
        return view('shop-admin.staff.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::where('slug', '!=', Role::SUPER_ADMIN)->get();
        return view('shop-admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $tenant = Auth::user()->tenant;
        
        if ($tenant->users()->count() >= $tenant->max_users) {
            return redirect()->back()->with('error', "You have reached your plan limit of {$tenant->max_users} users.");
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => 'required|exists:roles,id',
        ]);

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role_id' => $validated['role_id'],
            'is_active' => true,
        ]);

        return redirect()->route('shop-admin.staff.index')->with('success', 'Staff member added successfully.');
    }

    public function edit(User $staff)
    {
        $roles = Role::where('slug', '!=', Role::SUPER_ADMIN)->get();
        return view('shop-admin.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, User $staff)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->id,
            'role_id' => 'required|exists:roles,id',
            'is_active' => 'required|boolean',
        ]);

        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8|confirmed']);
            $staff->password = Hash::make($request->password);
        }

        $staff->update([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'role_id' => $validated['role_id'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('shop-admin.staff.index')->with('success', 'Staff member updated successfully.');
    }

    public function destroy(User $staff)
    {
        if ($staff->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $staff->delete();
        return redirect()->route('shop-admin.staff.index')->with('success', 'Staff member deleted successfully.');
    }
}
