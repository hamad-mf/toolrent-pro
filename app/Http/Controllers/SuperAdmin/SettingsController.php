<?php

namespace App\Http\Controllers\SuperAdmin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    public function index()
    {
        $settings = [
            'platform_name' => Setting::get('platform_name', 'ToolRent Pro'),
            'support_email' => Setting::get('support_email', 'support@toolrent.com'),
            'default_plan' => Setting::get('default_plan', 'Basic'),
            'maintenance_mode' => Setting::get('maintenance_mode', '0'),
        ];

        return view('super-admin.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'platform_name' => 'required|string|max:255',
            'support_email' => 'required|email|max:255',
            'default_plan' => 'required|string|in:Basic,Standard,Premium',
            'maintenance_mode' => 'nullable|boolean',
        ]);

        Setting::set('platform_name', $validated['platform_name']);
        Setting::set('support_email', $validated['support_email']);
        Setting::set('default_plan', $validated['default_plan']);
        Setting::set('maintenance_mode', $request->boolean('maintenance_mode') ? '1' : '0');

        return redirect()->route('super-admin.settings.index')->with('success', 'Global settings updated successfully.');
    }
}
