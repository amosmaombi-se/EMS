<?php

namespace App\Http\Controllers;

use App\Models\VendorCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Exception;

class VendorCategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = VendorCategory::withCount('vendors')
                ->orderBy('sort_order')
                ->paginate(20)
                ->withQueryString();

            return inertia('VendorCategories/Index', [
                'categories' => $categories,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch vendor categories: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to fetch categories. Please try again.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'        => 'required|string|max:255|unique:vendor_categories,name',
                'description' => 'nullable|string',
                'icon'        => 'nullable|string|max:100',
                'sort_order'  => 'nullable|integer|min:0',
                'is_active'   => 'boolean',
            ]);

            $validated['slug']      = Str::slug($validated['name']);
            $validated['is_active'] = $validated['is_active'] ?? true;

            $category = VendorCategory::create($validated);

            Log::info("Vendor category created: {$category->name}");

            return redirect()->back()->with('success', 'Category created successfully.');
        } catch (Exception $e) {
            Log::error('Failed to create vendor category: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to create category.')->withInput();
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = VendorCategory::findOrFail($id);

            $validated = $request->validate([
                'name'        => ['sometimes', 'required', 'string', 'max:255',
                                  \Illuminate\Validation\Rule::unique('vendor_categories', 'name')->ignore($id)],
                'description' => 'nullable|string',
                'icon'        => 'nullable|string|max:100',
                'sort_order'  => 'nullable|integer|min:0',
                'is_active'   => 'sometimes|boolean',
            ]);

            if (isset($validated['name'])) {
                $validated['slug'] = Str::slug($validated['name']);
            }

            $category->update($validated);

            return redirect()->back()->with('success', 'Category updated successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Category not found.');
        } catch (Exception $e) {
            Log::error('Failed to update vendor category: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to update category.');
        }
    }

    public function destroy($id)
    {
        try {
            $category = VendorCategory::findOrFail($id);

            if ($category->vendors()->exists()) {
                return redirect()->back()
                    ->with('error', 'Cannot delete category with existing vendors.');
            }

            $category->delete();

            Log::info("Vendor category deleted: {$category->name}");

            return redirect()->back()->with('success', 'Category deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->back()->with('error', 'Category not found.');
        } catch (Exception $e) {
            Log::error('Failed to delete vendor category: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete category.');
        }
    }
}