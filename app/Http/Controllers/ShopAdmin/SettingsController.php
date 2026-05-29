<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function edit()
    {
        $tenant = Auth::user()->tenant;
        return view('shop-admin.settings.edit', compact('tenant'));
    }

    public function update(Request $request)
    {
        $tenant = Auth::user()->tenant;

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'system_name' => 'nullable|string|max:255',
            'primary_color' => 'required|string|max:7',
            'secondary_color' => 'nullable|string|max:7',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|file|mimes:ico,png,jpg,jpeg,svg|max:1024',
            'custom_css' => 'nullable|string|max:10000',
        ]);

        if ($request->hasFile('logo')) {
            $path = $request->file('logo')->store('logos', 'public');
            $validated['logo'] = $path;
        }

        if ($request->hasFile('favicon')) {
            $validated['favicon'] = $request->file('favicon')->store('favicons', 'public');
        }

        $tenant->update($validated);

        // Update session so changes reflect immediately
        session()->put('tenant_name', $tenant->name);
        session()->put('tenant_primary_color', $tenant->primary_color);

        return redirect()->route('shop-admin.settings.edit')->with('success', 'Shop settings updated successfully.');
    }
}
