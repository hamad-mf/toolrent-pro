<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Tenant;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function index()
    {
        $tenants = Tenant::latest()->paginate(10);
        return view('super-admin.tenants.index', compact('tenants'));
    }

    public function create()
    {
        $defaultPlan = Setting::get('default_plan', 'Basic');

        return view('super-admin.tenants.create', compact('defaultPlan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|alpha_dash|unique:tenants',
            'plan' => 'required|string|in:Basic,Standard,Premium',
            'max_users' => 'required|integer|min:1',
            'max_tools' => 'required|integer|min:1',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'system_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,svg|max:1024',
            'custom_css' => 'nullable|string|max:10000',
            'features' => 'nullable|array',
            'features.*' => 'string|in:categories,tools,customers,rentals,reports,invoicing,qrcode',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('favicon')) {
            $validated['favicon'] = $request->file('favicon')->store('favicons', 'public');
        }

        $validated['features'] = $validated['features'] ?? [];

        Tenant::create($validated);

        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant created successfully.');
    }

    public function edit(Tenant $tenant)
    {
        return view('super-admin.tenants.edit', compact('tenant'));
    }

    public function update(Request $request, Tenant $tenant)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|alpha_dash|unique:tenants,slug,' . $tenant->id,
            'plan' => 'required|string|in:Basic,Standard,Premium',
            'max_users' => 'required|integer|min:1',
            'max_tools' => 'required|integer|min:1',
            'primary_color' => 'nullable|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'system_name' => 'nullable|string|max:255',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,svg|max:1024',
            'custom_css' => 'nullable|string|max:10000',
            'is_active' => 'required|boolean',
            'features' => 'nullable|array',
            'features.*' => 'string|in:categories,tools,customers,rentals,reports,invoicing,qrcode',
        ]);

        if ($request->hasFile('logo')) {
            $validated['logo'] = $request->file('logo')->store('logos', 'public');
        }

        if ($request->hasFile('favicon')) {
            $validated['favicon'] = $request->file('favicon')->store('favicons', 'public');
        }

        $validated['features'] = $validated['features'] ?? [];

        $tenant->update($validated);

        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant updated successfully.');
    }

    public function inspect(Tenant $tenant)
    {
        // View summary data for a specific shop
        $tools = \App\Models\Tool::where('tenant_id', $tenant->id)->get();
        $customers = \App\Models\Customer::where('tenant_id', $tenant->id)->get();
        $rentals = \App\Models\Rental::where('tenant_id', $tenant->id)->latest()->take(20)->get();

        return view('super-admin.tenants.inspect', compact('tenant', 'tools', 'customers', 'rentals'));
    }

    public function destroy(Tenant $tenant)
    {
        $tenant->delete();
        return redirect()->route('super-admin.tenants.index')->with('success', 'Tenant deleted successfully.');
    }
}
