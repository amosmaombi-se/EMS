<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\Event;
use App\Models\Venue;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $status = $request->input('status');
            $paymentStatus = $request->input('payment_status');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $bookingsQuery = Booking::with(['user', 'event', 'venue']);

            // Filter by current user if not admin
            if (!auth()->user()->hasRole('admin')) {
                $bookingsQuery->where('user_id', auth()->id());
            }

            if ($search) {
                $bookingsQuery->where(function ($query) use ($search) {
                    $query->where('booking_number', 'like', '%' . $search . '%')
                        ->orWhereHas('event', function ($q) use ($search) {
                            $q->where('title', 'like', '%' . $search . '%');
                        })
                        ->orWhereHas('venue', function ($q) use ($search) {
                            $q->where('name', 'like', '%' . $search . '%');
                        });
                });
            }

            if ($status) {
                $bookingsQuery->where('status', $status);
            }

            if ($paymentStatus) {
                $bookingsQuery->where('payment_status', $paymentStatus);
            }

            if ($startDate) {
                $bookingsQuery->where('event_date', '>=', $startDate);
            }

            if ($endDate) {
                $bookingsQuery->where('event_date', '<=', $endDate);
            }

            $bookings = $bookingsQuery->orderBy('created_at', 'desc')
                ->paginate(15)
                ->withQueryString();

            return inertia('Bookings/Index', [
                'bookings' => $bookings,
                'filters' => [
                    'search' => $search,
                    'status' => $status,
                    'payment_status' => $paymentStatus,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch bookings: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to fetch bookings. Please try again.');
        }
    }

    public function create()
    {
        try {
            $events = Event::where('user_id', auth()->id())
                ->whereIn('status', ['draft', 'planning', 'confirmed'])
                ->get(['id', 'title', 'event_date']);

            $venues = Venue::where('is_active', true)
                ->where('is_verified', true)
                ->get(['id', 'name', 'type', 'capacity_max', 'base_price_per_day']);

            $vendors = \App\Models\Vendor::where('is_active', true)
                ->where('verification_status', 'verified')
                ->with('services')
                ->get();

            return inertia('Bookings/Create', [
                'events' => $events,
                'venues' => $venues,
                'vendors' => $vendors,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to load booking form: ' . $e->getMessage());
            return redirect()->route('bookings.index')
                ->with('error', 'Failed to load booking form. Please try again.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_id' => 'required|exists:events,id',
                'venue_id' => 'nullable|exists:venues,id',
                'event_date' => 'required|date|after:today',
                'event_end_date' => 'nullable|date|after_or_equal:event_date',
                'items' => 'required|array|min:1',
                'items.*.itemable_type' => 'required|string',
                'items.*.itemable_id' => 'required|integer',
                'items.*.quantity' => 'required|integer|min:1',
                'customer_notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            // Create booking
            $booking = Booking::create([
                'event_id' => $validated['event_id'],
                'user_id' => auth()->id(),
                'venue_id' => $validated['venue_id'],
                'event_date' => $validated['event_date'],
                'event_end_date' => $validated['event_end_date'],
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'customer_notes' => $validated['customer_notes'],
            ]);

            // Add booking items
            foreach ($validated['items'] as $itemData) {
                $itemable = $itemData['itemable_type']::findOrFail($itemData['itemable_id']);
                
                \App\Models\BookingItem::create([
                    'booking_id' => $booking->id,
                    'itemable_type' => $itemData['itemable_type'],
                    'itemable_id' => $itemData['itemable_id'],
                    'item_name' => $itemable->name ?? $itemable->business_name,
                    'description' => $itemable->description ?? '',
                    'quantity' => $itemData['quantity'],
                    'unit_price' => $itemable->base_price ?? $itemable->base_price_per_day,
                ]);
            }

            // Calculate totals
            $booking->calculateTotals();

            DB::commit();

            Log::info("Booking created: {$booking->booking_number}", [
                'booking_id' => $booking->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create booking: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create booking. Please try again.')
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $booking = Booking::with([
                'user',
                'event',
                'venue',
                'items.itemable',
                'payments',
                'invoices',
                'statusHistory.user'
            ])->findOrFail($id);

            // Authorization check
            if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('bookings.index')
                    ->with('error', 'Unauthorized');
            }

            return inertia('Bookings/Show', [
                'booking' => $booking
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking not found');
        } catch (Exception $e) {
            Log::error('Failed to fetch booking details: ' . $e->getMessage());
            return redirect()->route('bookings.index')
                ->with('error', 'Failed to fetch booking details. Please try again.');
        }
    }

    public function confirm($id)
    {
        try {
            $booking = Booking::findOrFail($id);

            // Authorization check
            if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('event-organizer')) {
                return redirect()->route('bookings.index')
                    ->with('error', 'Unauthorized');
            }

            if ($booking->status !== 'pending') {
                return redirect()->route('bookings.show', $booking->id)
                    ->with('error', 'Only pending bookings can be confirmed');
            }

            DB::beginTransaction();

            $booking->confirm();

            // Process booking confirmation
            \App\Jobs\ProcessBookingConfirmation::dispatch($booking);

            DB::commit();

            Log::info("Booking confirmed: {$booking->booking_number}", [
                'booking_id' => $booking->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking confirmed successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('bookings.index')
                ->with('error', 'Booking not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to confirm booking: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to confirm booking. Please try again.');
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);

            // Authorization check
            if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('bookings.index')
                    ->with('error', 'Unauthorized');
            }

            $validated = $request->validate([
                'cancellation_reason' => 'required|string|max:500',
            ]);

            DB::beginTransaction();

            $booking->cancel($validated['cancellation_reason']);

            DB::commit();

            Log::info("Booking cancelled: {$booking->booking_number}", [
                'booking_id' => $booking->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking cancelled successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('bookings.index')
                ->with('error', 'Booking not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel booking: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to cancel booking. Please try again.')
                ->withInput();
        }
    }

    public function complete($id)
    {
        try {
            $booking = Booking::findOrFail($id);

            // Authorization check
            if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('event-organizer')) {
                return redirect()->route('bookings.index')
                    ->with('error', 'Unauthorized');
            }

            if (!in_array($booking->status, ['confirmed', 'in_progress'])) {
                return redirect()->route('bookings.show', $booking->id)
                    ->with('error', 'Only confirmed or in-progress bookings can be completed');
            }

            DB::beginTransaction();

            $booking->complete();

            DB::commit();

            Log::info("Booking completed: {$booking->booking_number}", [
                'booking_id' => $booking->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking completed successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('bookings.index')
                ->with('error', 'Booking not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete booking: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to complete booking. Please try again.');
        }
    }
}
