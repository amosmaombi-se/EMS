<?php

namespace App\Http\Controllers;

use App\Models\Venue;
use App\Models\VenueAvailability;
use App\Models\Booking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\Rule;
use Exception;


class VenueController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $type = $request->input('type');
            $city = $request->input('city');
            $minCapacity = $request->input('min_capacity');
            $maxCapacity = $request->input('max_capacity');
            $minPrice = $request->input('min_price');
            $maxPrice = $request->input('max_price');
            $isVerified = $request->input('is_verified');
            $isFeatured = $request->input('is_featured');

            $venuesQuery = Venue::with(['user']);

            if ($search) {
                $venuesQuery->where(function ($query) use ($search) {
                    $query->where('name', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('address', 'like', '%' . $search . '%')
                        ->orWhere('city', 'like', '%' . $search . '%');
                });
            }

            if ($type) {
                $venuesQuery->where('type', $type);
            }

            if ($city) {
                $venuesQuery->where('city', $city);
            }

            if ($minCapacity) {
                $venuesQuery->where('capacity_max', '>=', $minCapacity);
            }

            if ($maxCapacity) {
                $venuesQuery->where('capacity_min', '<=', $maxCapacity);
            }

            if ($minPrice) {
                $venuesQuery->where('base_price_per_day', '>=', $minPrice);
            }

            if ($maxPrice) {
                $venuesQuery->where('base_price_per_day', '<=', $maxPrice);
            }

            if ($isVerified !== null) {
                $venuesQuery->where('is_verified', $isVerified);
            }

            if ($isFeatured !== null) {
                $venuesQuery->where('is_featured', $isFeatured);
            }

            $venues = $venuesQuery->where('is_active', true)
                ->orderBy('is_featured', 'desc')
                ->orderBy('rating', 'desc')
                ->paginate(12)
                ->withQueryString();

            return inertia('Venues/Index', [
                'venues' => $venues,
                'filters' => [
                    'search' => $search ?? '',
                    'type' => $type ?? '',
                    'city' => $city ?? '',
                    'min_capacity' => $minCapacity ?? '',
                    'max_capacity' => $maxCapacity ?? '',
                    'min_price' => $minPrice ?? '',
                    'max_price' => $maxPrice ?? '',
                    'is_verified' => $isVerified ?? '',
                    'is_featured' => $isFeatured ?? '',
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch venues: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to fetch venues. Please try again.');
        }
    }

    public function create()
    {
        return inertia('Venues/Create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'type' => ['required', Rule::in(['indoor', 'outdoor', 'banquet_hall', 'garden', 'rooftop', 'beach', 'hotel', 'restaurant', 'other'])],
                'address' => 'required|string',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'country' => 'required|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'latitude' => 'nullable|numeric|between:-90,90',
                'longitude' => 'nullable|numeric|between:-180,180',
                'contact_person' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'capacity_min' => 'required|integer|min:0',
                'capacity_max' => 'required|integer|gt:capacity_min',
                'area_sqft' => 'nullable|numeric|min:0',
                'base_price_per_day' => 'required|numeric|min:0',
                'base_price_per_hour' => 'nullable|numeric|min:0',
                'security_deposit' => 'nullable|numeric|min:0',
                'amenities' => 'nullable|array',
                'images' => 'nullable|array',
                'virtual_tour_url' => 'nullable|url',
                'terms_and_conditions' => 'nullable|string',
                'cancellation_policy' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $validated['user_id'] = auth()->id();
            $validated['slug'] = \Str::slug($validated['name'] . '-' . uniqid());
            $validated['is_active'] = true;
            $validated['is_verified'] = false;

            $venue = Venue::create($validated);

            // Generate availability for next 180 days
            for ($i = 0; $i < 180; $i++) {
                VenueAvailability::create([
                    'venue_id' => $venue->id,
                    'date' => now()->addDays($i),
                    'status' => 'available',
                ]);
            }

            DB::commit();

            Log::info("Venue created: {$venue->name}", ['venue_id' => $venue->id, 'user_id' => auth()->id()]);

            return redirect()->route('venues.show', $venue->id)
                ->with('success', 'Venue created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create venue: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create venue. Please try again.')
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $venue = Venue::with(['user', 'reviews.user'])
                ->withCount(['bookings', 'reviews'])
                ->findOrFail($id);

            $venue->load([
                'availability' => function ($query) {
                    $query->where('date', '>=', now())
                        ->orderBy('date')
                        ->limit(30);
                }
            ]);

            return inertia('Venues/Show', [
                'venue' => $venue
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('venues.index')
                ->with('error', 'Venue not found');
        } catch (Exception $e) {
            Log::error('Failed to fetch venue: ' . $e->getMessage());
            return redirect()->route('venues.index')
                ->with('error', 'Failed to fetch venue details. Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $venue = Venue::findOrFail($id);

            // Authorization check
            if ($venue->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('venues.index')
                    ->with('error', 'Unauthorized');
            }

            return inertia('Venues/Edit', [
                'venue' => $venue
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('venues.index')
                ->with('error', 'Venue not found');
        } catch (Exception $e) {
            Log::error('Failed to load venue edit form: ' . $e->getMessage());
            return redirect()->route('venues.index')
                ->with('error', 'Failed to load venue. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $venue = Venue::findOrFail($id);

            // Authorization check
            if ($venue->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('venues.index')
                    ->with('error', 'Unauthorized');
            }

            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'type' => ['sometimes', Rule::in(['indoor', 'outdoor', 'banquet_hall', 'garden', 'rooftop', 'beach', 'hotel', 'restaurant', 'other'])],
                'address' => 'sometimes|required|string',
                'city' => 'sometimes|required|string|max:100',
                'state' => 'sometimes|required|string|max:100',
                'country' => 'sometimes|required|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'contact_person' => 'sometimes|required|string|max:255',
                'email' => 'sometimes|required|email|max:255',
                'phone' => 'sometimes|required|string|max:20',
                'capacity_min' => 'sometimes|required|integer|min:0',
                'capacity_max' => 'sometimes|required|integer',
                'base_price_per_day' => 'sometimes|required|numeric|min:0',
                'amenities' => 'nullable|array',
                'images' => 'nullable|array',
                'is_active' => 'sometimes|boolean',
            ]);

            DB::beginTransaction();

            $venue->update($validated);

            DB::commit();

            Log::info("Venue updated: {$venue->name}", ['venue_id' => $venue->id, 'user_id' => auth()->id()]);

            return redirect()->route('venues.show', $venue->id)
                ->with('success', 'Venue updated successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('venues.index')
                ->with('error', 'Venue not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update venue: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update venue. Please try again.')
                ->withInput();
        }
    }

    public function destroy($id)
    {
        try {
            $booking = Booking::findOrFail($id);

            // FIX: block deletion if booking has active status — mirrors the Venue logic
            if (in_array($booking->status, ['confirmed', 'in_progress'])) {
                return redirect()->route('bookings.show', $booking->id)
                    ->with('error', 'Cannot delete a confirmed or in-progress booking. Cancel it first.');
            }

            DB::beginTransaction();

            $bookingNumber = $booking->booking_number;

            $booking->items()->delete();
            $booking->payments()->delete();
            $booking->invoices()->delete();

            $booking->delete();

            DB::commit();

            Log::info("Booking deleted: {$bookingNumber}", [
                'booking_id' => $id,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('bookings.index')
                ->with('success', "Booking {$bookingNumber} deleted successfully");

        } catch (ModelNotFoundException $e) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking not found');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete booking: ' . $e->getMessage(), [
                'booking_id' => $id,
                'user_id' => auth()->id(),
            ]);
            return redirect()->back()
                ->with('error', 'Failed to delete booking. Please try again.');
        }
    }


    public function checkAvailability(Request $request, $id)
    {
        try {
            $validated = $request->validate([
                'start_date' => 'required|date',
                'end_date' => 'required|date|after_or_equal:start_date',
            ]);

            $venue = Venue::findOrFail($id);

            $availability = VenueAvailability::where('venue_id', $venue->id)
                ->whereBetween('date', [$validated['start_date'], $validated['end_date']])
                ->get();

            $isAvailable = $availability->every(function ($item) {
                return $item->status === 'available';
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'is_available' => $isAvailable,
                    'availability' => $availability,
                    'total_days' => $availability->count(),
                    'available_days' => $availability->where('status', 'available')->count(),
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Failed to check venue availability: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Failed to check availability'
            ], 500);
        }
    }
}
