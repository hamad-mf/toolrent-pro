<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Tool;
use App\Models\Category;
use Illuminate\Http\Request;

class ToolController extends Controller
{
    public function index()
    {
        $tools = Tool::with('category')->latest()->paginate(10);
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
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Rented,Maintenance',
            'image' => 'nullable|image|max:2048',
        ]);

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
            'category_id' => 'required|exists:categories,id',
            'name' => 'required|string|max:255',
            'brand' => 'nullable|string|max:255',
            'model_number' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'daily_rate' => 'required|numeric|min:0',
            'status' => 'required|in:Available,Rented,Maintenance',
            'image' => 'nullable|image|max:2048',
        ]);

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
        if ($tool->status === 'Rented') {
            return redirect()->back()->with('error', 'Cannot set maintenance on a tool that is currently rented.');
        }

        $newStatus = ($tool->status === 'Maintenance') ? 'Available' : 'Maintenance';
        $tool->update(['status' => $newStatus]);

        $msg = ($newStatus === 'Maintenance') ? 'Tool sent for maintenance.' : 'Tool marked as available.';
        return redirect()->back()->with('success', $msg);
    }
}
