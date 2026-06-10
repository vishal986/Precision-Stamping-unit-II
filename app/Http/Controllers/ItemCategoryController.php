<?php

namespace App\Http\Controllers;

use App\Models\ItemCategory;
use Illuminate\Http\Request;

class ItemCategoryController extends Controller
{
    public function index()
    {
        $categories = ItemCategory::orderBy('name')->get();
        return view('items.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:item_categories,name',
            'description' => 'nullable|string'
        ]);

        ItemCategory::create($validated);

        return back()->with('success', 'Category added successfully.');
    }

    public function destroy(ItemCategory $itemCategory)
    {
        // Prevent deletion if items are using this category
        if (\App\Models\Item::where('item_category_id', $itemCategory->id)->exists()) {
            return back()->with('error', 'Cannot delete category because it is assigned to one or more items.');
        }

        $itemCategory->delete();
        return back()->with('success', 'Category deleted successfully.');
    }
}
