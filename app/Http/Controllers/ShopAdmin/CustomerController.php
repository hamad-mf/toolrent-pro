<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = Customer::withCount('rentals')->latest()->paginate(10);
        return view('shop-admin.customers.index', compact('customers'));
    }

    public function create()
    {
        return view('shop-admin.customers.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'id_type' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        Customer::create($validated);

        return redirect()->route('shop-admin.customers.index')->with('success', 'Customer added successfully.');
    }

    public function edit(Customer $customer)
    {
        $rentals = $customer->rentals()->with('tool')->latest()->take(10)->get();

        return view('shop-admin.customers.edit', compact('customer', 'rentals'));
    }

    public function update(Request $request, Customer $customer)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
            'id_type' => 'nullable|string|max:50',
            'id_number' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'required|boolean',
        ]);

        $customer->update($validated);

        return redirect()->route('shop-admin.customers.index')->with('success', 'Customer updated successfully.');
    }

    public function destroy(Customer $customer)
    {
        if ($customer->rentals()->exists()) {
            return redirect()->back()->with('error', 'Cannot delete customer because rental history exists. Deactivate the customer instead.');
        }

        $customer->delete();
        return redirect()->route('shop-admin.customers.index')->with('success', 'Customer deleted successfully.');
    }
}
