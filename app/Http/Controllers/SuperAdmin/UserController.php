<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function index()
    {
        $users = User::with(['role', 'tenant'])->withCount('processedRentals')->latest()->paginate(15);
        return view('super-admin.users.index', compact('users'));
    }

    public function destroy(User $user)
    {
        if ($user->id === auth()->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        if ($user->processedRentals()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete a user with rental history. Deactivate the account from staff management instead.');
        }

        $user->delete();
        return redirect()->route('super-admin.users.index')->with('success', 'User deleted successfully.');
    }
}
