<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;


class VendorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $categoryId = $request->input('category_id');
            $city = $request->input('city');
            $minRating = $request->input('min_rating');
            $isVerified = $request->input('is_verified');
            $isFeatured = $request->input('is_featured');

            $vendorsQuery = \App\Models\Vendor::with(['category', 'user', 'services']);

            if ($search) {
                $vendorsQuery->where(function ($query) use ($search) {
                    $query->where('business_name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('city', 'like', '%' . $search . '%');
                });
            }

            if ($categoryId) {
                $vendorsQuery->where('vendor_category_id', $categoryId);
            }

            if ($city) {
                $vendorsQuery->where('city', $city);
            }

            if ($minRating) {
                $vendorsQuery->where('rating', '>=', $minRating);
            }

            if ($isVerified !== null) {
                $vendorsQuery->where('verification_status', 'verified');
            }

            if ($isFeatured !== null) {
                $vendorsQuery->where('is_featured', $isFeatured);
            }

            $vendors = $vendorsQuery->where('is_active', true)
                ->orderBy('is_featured', 'desc')
                ->orderBy('rating', 'desc')
                ->paginate(12)
                ->withQueryString();

            $categories = \App\Models\VendorCategory::where('is_active', true)->get();

            return inertia('Vendors/Index', [
                'vendors' => $vendors,
                'categories' => $categories,
                'filters' => [
                    'search' => $search,
                    'category_id' => $categoryId,
                    'city' => $city,
                    'min_rating' => $minRating,
                    'is_verified' => $isVerified,
                    'is_featured' => $isFeatured,
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch vendors: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to fetch vendors. Please try again.');
        }
    }

    public function show($id)
    {
        try {
            $vendor = \App\Models\Vendor::with([
                'category',
                'user',
                'services',
                'portfolios.eventType',
                'reviews.user',
                'availability'
            ])->findOrFail($id);

            $vendor->load(['availability' => function ($query) {
                $query->where('date', '>=', now())
                    ->orderBy('date')
                    ->limit(30);
            }]);

            return inertia('Vendors/Show', [
                'vendor' => $vendor
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('vendors.index')
                ->with('error', 'Vendor not found');
        } catch (Exception $e) {
            Log::error('Failed to fetch vendor details: ' . $e->getMessage());
            return redirect()->route('vendors.index')
                ->with('error', 'Failed to fetch vendor details. Please try again.');
        }
    }
}

