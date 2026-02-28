<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use App\Models\VendorCategory;
use App\Models\VendorAvailability;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Exception;

class VendorController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search             = $request->input('search');
            $categoryId         = $request->input('category_id');
            $city               = $request->input('city');
            $verificationStatus = $request->input('verification_status');
            $isFeatured         = $request->input('is_featured');
            $minRating          = $request->input('min_rating');
            $perPage            = in_array((int) $request->input('per_page'), [10, 25, 50, 100])
                                    ? (int) $request->input('per_page') : 25;
            $sortColumn         = in_array($request->input('sort'), [
                                    'business_name', 'city', 'rating', 'minimum_order_value', 'total_bookings',
                                  ]) ? $request->input('sort') : null;
            $direction          = $request->input('direction') === 'desc' ? 'desc' : 'asc';

            $query = Vendor::with(['category', 'user'])
                ->withCount(['reviews', 'bookingItems']);

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('business_name', 'like', "%{$search}%")
                      ->orWhere('description', 'like', "%{$search}%")
                      ->orWhere('city', 'like', "%{$search}%");
                });
            }

            if ($categoryId) {
                $query->where('vendor_category_id', $categoryId);
            }

            if ($city) {
                $query->where('city', 'like', "%{$city}%");
            }

            if ($verificationStatus) {
                $query->where('verification_status', $verificationStatus);
            }

            if ($isFeatured !== null && $isFeatured !== '') {
                $query->where('is_featured', (bool) $isFeatured);
            }

            if ($minRating) {
                $query->where('rating', '>=', $minRating);
            }

            $vendors = $query
                ->when($sortColumn, fn($q) => $q->orderBy($sortColumn, $direction),
                    fn($q) => $q->orderBy('is_featured', 'desc')->orderBy('rating', 'desc'))
                ->paginate($perPage)
                ->withQueryString();

            $categories = VendorCategory::active()->orderBy('sort_order')->get();

            return inertia('Vendors/Index', [
                'vendors'    => $vendors,
                'categories' => $categories,
                'filters'    => [
                    'search'              => $search ?? '',
                    'category_id'         => $categoryId ?? '',
                    'city'                => $city ?? '',
                    'verification_status' => $verificationStatus ?? '',
                    'is_featured'         => $isFeatured ?? '',
                    'min_rating'          => $minRating ?? '',
                    'per_page'            => $perPage,
                    'sort'                => $sortColumn ?? '',
                    'direction'           => $direction,
                ],
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch vendors: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to fetch vendors. Please try again.');
        }
    }

    public function create()
    {
        $categories = VendorCategory::active()->orderBy('sort_order')->get();

        return inertia('Vendors/Create', [
            'categories' => $categories,
        ]);
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'vendor_category_id'          => 'required|exists:vendor_categories,id',
                'business_name'               => 'required|string|max:255',
                'description'                 => 'nullable|string',
                'contact_person'              => 'required|string|max:255',
                'email'                       => 'required|email|max:255',
                'phone'                       => 'required|string|max:20',
                'website'                     => 'nullable|url|max:255',
                'address'                     => 'required|string',
                'city'                        => 'required|string|max:100',
                'state'                       => 'required|string|max:100',
                'country'                     => 'required|string|max:100',
                'postal_code'                 => 'nullable|string|max:20',
                'business_registration_number'=> 'nullable|string|max:100',
                'tax_id'                      => 'nullable|string|max:100',
                'minimum_order_value'         => 'nullable|numeric|min:0',
                'years_of_experience'         => 'nullable|integer|min:0',
                'team_size'                   => 'nullable|integer|min:1',
                'service_areas'               => 'nullable|array',
                'service_areas.*'             => 'string|max:100',
                'specializations'             => 'nullable|array',
                'specializations.*'           => 'string|max:100',
                'is_featured'                 => 'boolean',
                'is_active'                   => 'boolean',
            ]);

            DB::beginTransaction();

            $validated['user_id']             = auth()->id();
            $validated['slug']                = Str::slug($validated['business_name'] . '-' . uniqid());
            $validated['verification_status'] = 'pending';
            $validated['is_active']           = $validated['is_active'] ?? true;
            $validated['is_featured']         = $validated['is_featured'] ?? false;

            $vendor = Vendor::create($validated);

            DB::commit();

            Log::info("Vendor created: {$vendor->business_name}", [
                'vendor_id' => $vendor->id,
                'user_id'   => auth()->id(),
            ]);

            return redirect()->route('vendors.show', $vendor->id)
                ->with('success', 'Vendor created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create vendor: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create vendor. Please try again.')
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $vendor = Vendor::with([
                'category',
                'user',
                'services',
                'portfolios',
                'reviews.user',
            ])
            ->withCount(['reviews', 'bookingItems'])
            ->findOrFail($id);

            // Load upcoming availability (next 30 days)
            $vendor->load(['availability' => function ($query) {
                $query->where('date', '>=', now())
                      ->orderBy('date')
                      ->limit(30);
            }]);

            return inertia('Vendors/Show', [
                'vendor' => $vendor,
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('vendors.index')->with('error', 'Vendor not found.');
        } catch (Exception $e) {
            Log::error('Failed to fetch vendor: ' . $e->getMessage());
            return redirect()->route('vendors.index')
                ->with('error', 'Failed to fetch vendor details. Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);

            if ($vendor->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('vendors.index')->with('error', 'Unauthorized.');
            }

            $categories = VendorCategory::active()->orderBy('sort_order')->get();

            return inertia('Vendors/Edit', [
                'vendor'     => $vendor,
                'categories' => $categories,
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('vendors.index')->with('error', 'Vendor not found.');
        } catch (Exception $e) {
            Log::error('Failed to load vendor edit form: ' . $e->getMessage());
            return redirect()->route('vendors.index')
                ->with('error', 'Failed to load vendor. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $vendor = Vendor::findOrFail($id);

            if ($vendor->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('vendors.index')->with('error', 'Unauthorized.');
            }

            $validated = $request->validate([
                'vendor_category_id'          => 'sometimes|required|exists:vendor_categories,id',
                'business_name'               => 'sometimes|required|string|max:255',
                'description'                 => 'nullable|string',
                'contact_person'              => 'sometimes|required|string|max:255',
                'email'                       => 'sometimes|required|email|max:255',
                'phone'                       => 'sometimes|required|string|max:20',
                'website'                     => 'nullable|url|max:255',
                'address'                     => 'sometimes|required|string',
                'city'                        => 'sometimes|required|string|max:100',
                'state'                       => 'sometimes|required|string|max:100',
                'country'                     => 'sometimes|required|string|max:100',
                'postal_code'                 => 'nullable|string|max:20',
                'business_registration_number'=> 'nullable|string|max:100',
                'tax_id'                      => 'nullable|string|max:100',
                'minimum_order_value'         => 'nullable|numeric|min:0',
                'years_of_experience'         => 'nullable|integer|min:0',
                'team_size'                   => 'nullable|integer|min:1',
                'service_areas'               => 'nullable|array',
                'service_areas.*'             => 'string|max:100',
                'specializations'             => 'nullable|array',
                'specializations.*'           => 'string|max:100',
                'is_featured'                 => 'sometimes|boolean',
                'is_active'                   => 'sometimes|boolean',
                'verification_status'         => ['sometimes', Rule::in(['pending', 'verified', 'rejected'])],
                'verification_notes'          => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Auto-set verified_at when status changes to verified
            if (
                isset($validated['verification_status']) &&
                $validated['verification_status'] === 'verified' &&
                $vendor->verification_status !== 'verified'
            ) {
                $validated['verified_at'] = now();
            }

            $vendor->update($validated);

            DB::commit();

            Log::info("Vendor updated: {$vendor->business_name}", [
                'vendor_id' => $vendor->id,
                'user_id'   => auth()->id(),
            ]);

            return redirect()->route('vendors.show', $vendor->id)
                ->with('success', 'Vendor updated successfully.');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('vendors.index')->with('error', 'Vendor not found.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update vendor: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update vendor. Please try again.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $vendor = Vendor::findOrFail($id);

            if ($vendor->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('vendors.index')->with('error', 'Unauthorized.');
            }

            $hasActiveBookings = $vendor->bookingItems()
                ->whereHas('booking', fn($q) => $q->whereIn('status', ['pending', 'confirmed', 'in_progress']))
                ->exists();

            if ($hasActiveBookings) {
                return redirect()->route('vendors.show', $vendor->id)
                    ->with('error', 'Cannot delete vendor with active bookings.');
            }

            DB::beginTransaction();

            $name = $vendor->business_name;
            $vendor->delete();

            DB::commit();

            Log::info("Vendor deleted: {$name}", ['user_id' => auth()->id()]);

            return redirect()->route('vendors.index')
                ->with('success', 'Vendor deleted successfully.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('vendors.index')->with('error', 'Vendor not found.');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete vendor: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to delete vendor. Please try again.');
        }
    }
}