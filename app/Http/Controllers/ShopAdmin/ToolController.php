<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ToolController extends Controller
{
    public function index(Request $request)
    {
        $tools = Tool::with('category')
            ->withCount('rentals')
            ->when($request->status, fn($q, $status) => $q->where('status', $status))
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('shop-admin.tools.index', compact('tools'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('shop-admin.tools.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->where('tenant_id', session('tenant_id'))],
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'condition_notes' => 'nullable|string|max:2000',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Maintenance',
            'image' => 'nullable|image|max:2048',
        ]);

        $tenant = auth()->user()->tenant;
        if ($tenant && Tool::count() >= $tenant->max_tools) {
            return redirect()->back()
                ->withInput()
                ->with('error', "You have reached your plan limit of {$tenant->max_tools} tools.");
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('tools', 'public');
            $validated['image'] = $path;
        }

        Tool::create($validated);

        return redirect()->route('shop-admin.tools.index')->with('success', 'Tool added successfully.');
    }

    public function show(Tool $tool)
    {
        return view('shop-admin.tools.show', compact('tool'));
    }

    public function edit(Tool $tool)
    {
        $categories = Category::all();
        return view('shop-admin.tools.edit', compact('tool', 'categories'));
    }

    public function update(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'category_id' => ['required', Rule::exists('categories', 'id')->where('tenant_id', session('tenant_id'))],
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'condition_notes' => 'nullable|string|max:2000',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Rented,Reserved,Maintenance',
            'image' => 'nullable|image|max:2048',
        ]);

        if ($tool->rentals()->whereIn('status', ['Pending', 'Active', 'Overdue'])->exists()
            && $validated['status'] !== $tool->status) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Cannot manually change status while this tool has an open booking or rental.');
        }

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('tools', 'public');
            $validated['image'] = $path;
        }

        $tool->update($validated);

        return redirect()->route('shop-admin.tools.index')->with('success', 'Tool updated successfully.');
    }

    public function destroy(Tool $tool)
    {
        if ($tool->rentals()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete tool because it has rental history. Deactivate it instead.');
        }

        $tool->delete();
        return redirect()->route('shop-admin.tools.index')->with('success', 'Tool deleted successfully.');
    }

    public function qrcode(Tool $tool)
    {
        // Generates a simple SVG QR Code with the tool's ID and name
        $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(200)->generate("ToolRentPro:ToolID:{$tool->id}");
        
        return view('shop-admin.tools.qrcode', compact('tool', 'qrCode'));
    }

    public function toggleMaintenance(Tool $tool)
    {
        if (in_array($tool->status, ['Rented', 'Reserved'])) {
            return redirect()->back()->with('error', 'Cannot set maintenance on a tool that is currently rented or reserved.');
        }

        $newStatus = ($tool->status === 'Maintenance') ? 'Available' : 'Maintenance';
        $tool->update([
            'status' => $newStatus,
            'condition_updated_at' => now(),
            'condition_updated_by' => auth()->id(),
        ]);

        $msg = ($newStatus === 'Maintenance') ? 'Tool sent for maintenance.' : 'Tool marked as available.';
        return redirect()->back()->with('success', $msg);
    }

    public function updateCondition(Request $request, Tool $tool)
    {
        $validated = $request->validate([
            'condition_notes' => 'nullable|string|max:2000',
            'status' => 'nullable|in:Available,Maintenance',
        ]);

        if ($tool->rentals()->whereIn('status', ['Pending', 'Active', 'Overdue'])->exists()
            && ($validated['status'] ?? $tool->status) !== $tool->status) {
            return redirect()->back()->with('error', 'Cannot change status while this tool has an open booking or rental.');
        }

        if ($tool->status === 'Rented' && ($validated['status'] ?? null) === 'Maintenance') {
            return redirect()->back()->with('error', 'Cannot send a rented tool to maintenance.');
        }

        if ($tool->status === 'Reserved' && ($validated['status'] ?? null) === 'Maintenance') {
            return redirect()->back()->with('error', 'Cannot send a reserved tool to maintenance.');
        }

        $updates = [
            'condition_notes' => $validated['condition_notes'] ?? null,
            'condition_updated_at' => now(),
            'condition_updated_by' => auth()->id(),
        ];

        if (array_key_exists('status', $validated) && $validated['status']) {
            $updates['status'] = $validated['status'];
        }

        $tool->update($updates);

        return redirect()->back()->with('success', 'Tool condition updated successfully.');
    }
}
