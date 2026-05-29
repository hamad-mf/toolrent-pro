<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class StaffController extends Controller
{
    public function index()
    {
        $users = User::with('role')
            ->withCount('processedRentals')
            ->whereHas('role', fn($query) => $query->whereNotIn('slug', [Role::SUPER_ADMIN, Role::SHOP_ADMIN]))
            ->latest()
            ->paginate(10);

        return view('shop-admin.staff.index', compact('users'));
    }

    public function create()
    {
        $roles = Role::whereNotIn('slug', [Role::SUPER_ADMIN, Role::SHOP_ADMIN])->get();
        return view('shop-admin.staff.create', compact('roles'));
    }

    public function store(Request $request)
    {
        $tenant = Auth::user()->tenant;
        
        if ($tenant->users()->count() >= $tenant->max_users) {
            return redirect()->back()->with('error', "You have reached your plan limit of {$tenant->max_users} users.");
        }

        $assignableRoleIds = Role::whereNotIn('slug', [Role::SUPER_ADMIN, Role::SHOP_ADMIN])->pluck('id')->all();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'role_id' => ['required', Rule::in($assignableRoleIds)],
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
        if ($staff->hasRole([Role::SUPER_ADMIN, Role::SHOP_ADMIN])) {
            abort(403, 'Shop owner accounts cannot be managed from staff management.');
        }

        $roles = Role::whereNotIn('slug', [Role::SUPER_ADMIN, Role::SHOP_ADMIN])->get();
        return view('shop-admin.staff.edit', compact('staff', 'roles'));
    }

    public function update(Request $request, User $staff)
    {
        if ($staff->hasRole([Role::SUPER_ADMIN, Role::SHOP_ADMIN])) {
            abort(403, 'Shop owner accounts cannot be managed from staff management.');
        }

        $assignableRoleIds = Role::whereNotIn('slug', [Role::SUPER_ADMIN, Role::SHOP_ADMIN])->pluck('id')->all();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $staff->id,
            'role_id' => ['required', Rule::in($assignableRoleIds)],
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

        if ($staff->hasRole([Role::SUPER_ADMIN, Role::SHOP_ADMIN])) {
            abort(403, 'Shop owner accounts cannot be managed from staff management.');
        }

        if ($staff->processedRentals()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete staff with rental history. Deactivate the account instead.');
        }

        $staff->delete();
        return redirect()->route('shop-admin.staff.index')->with('success', 'Staff member deleted successfully.');
    }
}
