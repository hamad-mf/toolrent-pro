<?php

namespace App\Http\Controllers\ShopAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('tools')->latest()->paginate(10);
        return view('shop-admin.categories.index', compact('categories'));
    }

    public function create()
    {
        return view('shop-admin.categories.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);
        
        Category::create($validated);

        return redirect()->route('shop-admin.categories.index')->with('success', 'Category created successfully.');
    }

    public function edit(Category $category)
    {
        return view('shop-admin.categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);
        
        $validated['slug'] = Str::slug($validated['name']);

        $category->update($validated);

        return redirect()->route('shop-admin.categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        if ($category->tools()->count() > 0) {
            return redirect()->back()->with('error', 'Cannot delete category because it contains tools.');
        }

        $category->delete();
        return redirect()->route('shop-admin.categories.index')->with('success', 'Category deleted successfully.');
    }
}
