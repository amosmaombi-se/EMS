<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Booking;
use App\Models\BookingItem;
use App\Models\Event;
use App\Models\Venue;
use App\Models\Vendor;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class BookingController extends Controller
{
    /**
     * FIX #6: Whitelist of allowed itemable types to prevent arbitrary class instantiation
     * from user-supplied input (critical security fix).
     */
    private const ITEMABLE_TYPE_MAP = [
        'venue'  => \App\Models\Venue::class,
        'vendor' => \App\Models\Vendor::class,
        // Add other allowed types here as needed
    ];

    public function index(Request $request)
    {
        try {
            $search        = $request->input('search');
            $status        = $request->input('status');
            $paymentStatus = $request->input('payment_status');
            $startDate     = $request->input('start_date');
            $endDate       = $request->input('end_date');
            $eventType     = $request->input('event_type');
            $minAmount     = $request->input('min_amount');
            $maxAmount     = $request->input('max_amount');

            $bookingsQuery = Booking::with(['user', 'event', 'venue']);

            // Scope to current user unless admin
            if (!auth()->user()->hasRole('admin')) {
                $bookingsQuery->where('user_id', auth()->id());
            }

            if ($search) {
                $bookingsQuery->where(function ($query) use ($search) {
                    $query->where('booking_number', 'like', '%' . $search . '%')
                        ->orWhereHas('event', fn($q) => $q->where('title', 'like', '%' . $search . '%'))
                        ->orWhereHas('venue', fn($q) => $q->where('name', 'like', '%' . $search . '%'));
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

            if ($eventType) {
                $bookingsQuery->whereHas('event', fn($q) => $q->where('type', $eventType));
            }

            if ($minAmount) {
                $bookingsQuery->where('total_amount', '>=', $minAmount);
            }

            if ($maxAmount) {
                $bookingsQuery->where('total_amount', '<=', $maxAmount);
            }

            $bookings = $bookingsQuery->orderBy('created_at', 'desc')->paginate(15)->withQueryString();

            // FIX #7: Scope stats to the current user unless admin
            $statsQuery = auth()->user()->hasRole('admin')
                ? Booking::query()
                : Booking::where('user_id', auth()->id());

            $stats = [
                'total'         => (clone $statsQuery)->count(),
                'total_revenue' => (clone $statsQuery)->sum('total_amount'),
                'pending'       => (clone $statsQuery)->where('status', 'pending')->count(),
                'confirmed'     => (clone $statsQuery)->where('status', 'confirmed')->count(),
                'overdue'       => (clone $statsQuery)
                    ->where('event_end_date', '<', now())
                    ->where('payment_status', '!=', 'paid')
                    ->count(),
            ];

            return inertia('Bookings/Index', [
                'bookings' => $bookings,
                'filters'  => compact(
                    'search', 'status', 'paymentStatus', 'startDate',
                    'endDate', 'eventType', 'minAmount', 'maxAmount'
                ),
                'stats'    => $stats,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch bookings: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to fetch bookings. Please try again.');
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

            $vendors = Vendor::where('is_active', true)
                ->where('verification_status', 'verified')
                ->with('services')
                ->get();

            return inertia('Bookings/Create', compact('events', 'venues', 'vendors'));
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
                'event_id'                  => 'required|exists:events,id',
                'venue_id'                  => 'nullable|exists:venues,id',
                'event_date'                => 'required|date|after:today',
                'event_end_date'            => 'nullable|date|after_or_equal:event_date',
                'items'                     => 'required|array|min:1',
                // FIX #6: Validate itemable_type against the whitelist
                'items.*.itemable_type'     => ['required', 'string', 'in:' . implode(',', array_keys(self::ITEMABLE_TYPE_MAP))],
                'items.*.itemable_id'       => 'required|integer',
                'items.*.quantity'          => 'required|integer|min:1',
                'customer_notes'            => 'nullable|string',
            ]);

            DB::beginTransaction();

            $booking = Booking::create([
                'event_id'        => $validated['event_id'],
                'user_id'         => auth()->id(),
                'venue_id'        => $validated['venue_id'],
                'event_date'      => $validated['event_date'],
                'event_end_date'  => $validated['event_end_date'] ?? null,
                'subtotal'        => 0,
                'tax_amount'      => 0,
                'discount_amount' => 0,
                'total_amount'    => 0,
                'paid_amount'     => 0,
                'due_amount'      => 0,
                'status'          => 'pending',
                'payment_status'  => 'unpaid',
                'customer_notes'  => $validated['customer_notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                // FIX #6: Resolve class from whitelist — never trust raw user input as a class name
                $modelClass = self::ITEMABLE_TYPE_MAP[$itemData['itemable_type']];
                $itemable   = $modelClass::findOrFail($itemData['itemable_id']);

                BookingItem::create([
                    'booking_id'     => $booking->id,
                    'itemable_type'  => $modelClass,
                    'itemable_id'    => $itemData['itemable_id'],
                    'item_name'      => $itemable->name ?? $itemable->business_name,
                    'description'    => $itemable->description ?? '',
                    'quantity'       => $itemData['quantity'],
                    'unit_price'     => $itemable->base_price ?? $itemable->base_price_per_day,
                ]);
            }

            $booking->calculateTotals();

            DB::commit();

            Log::info("Booking created: {$booking->booking_number}", [
                'booking_id' => $booking->id,
                'user_id'    => auth()->id(),
            ]);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create booking: ' . $e->getMessage(), [
                'request_data' => $request->all(),
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
            ])->findOrFail($id);

            if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('bookings.index')->with('error', 'Unauthorized');
            }

            return inertia('Bookings/Show', compact('booking'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('bookings.index')->with('error', 'Booking not found');
        } catch (Exception $e) {
            Log::error('Failed to fetch booking details: ' . $e->getMessage());
            return redirect()->route('bookings.index')
                ->with('error', 'Failed to fetch booking details. Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $booking = Booking::with(['event', 'venue', 'items'])->findOrFail($id);

            if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('bookings.index')->with('error', 'Unauthorized');
            }

            if (in_array($booking->status, ['completed', 'cancelled'])) {
                return redirect()->route('bookings.show', $booking->id)
                    ->with('error', 'Completed or cancelled bookings cannot be edited');
            }

            $events = Event::where('user_id', auth()->id())
                ->whereIn('status', ['draft', 'planning', 'confirmed'])
                ->get(['id', 'title', 'event_date']);

            $venues = Venue::where('is_active', true)
                ->where('is_verified', true)
                ->get(['id', 'name', 'type', 'capacity_max', 'base_price_per_day']);

            return inertia('Bookings/Edit', compact('booking', 'events', 'venues'));
        } catch (ModelNotFoundException $e) {
            return redirect()->route('bookings.index')->with('error', 'Booking not found');
        } catch (Exception $e) {
            Log::error('Failed to load booking edit form: ' . $e->getMessage());
            return redirect()->route('bookings.index')
                ->with('error', 'Failed to load booking. Please try again.');
        }
    }

    public function update(Request $request, $id)
{
    try {
        $booking = Booking::findOrFail($id);

        // Authorization
        if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            return redirect()->route('bookings.index')->with('error', 'Unauthorized');
        }

        if (in_array($booking->status, ['completed', 'cancelled'])) {
            return redirect()->route('bookings.show', $booking->id)
                ->with('error', 'Completed or cancelled bookings cannot be edited');
        }

        // FIX #1: Validation now covers ALL fields the Edit.vue form submits.
        // The original only validated event_id/venue_id/event_date/event_end_date/customer_notes
        // but the form also sends: status, payment_status, total_amount, paid_amount,
        // due_date, internal_notes, and items[].
        $validated = $request->validate([
            // Core booking fields
            'venue_id'          => 'nullable|exists:venues,id',
            'event_date'        => 'required|date',
            'event_end_date'    => 'nullable|date|after_or_equal:event_date',

            // FIX #2: event_id was required in original but the Edit form does NOT send it
            // (the event is shown as read-only / fixed). Removed to prevent validation failure.

            // Status fields — sent by the form
            'status'            => 'required|in:pending,confirmed,in_progress,completed,cancelled',
            'payment_status'    => 'required|in:unpaid,partially_paid,paid,refunded',

            // Financial fields — sent by the form
            'total_amount'      => 'nullable|numeric|min:0',
            'paid_amount'       => 'nullable|numeric|min:0|lte:total_amount',
            'due_date'          => 'nullable|date',

            // Notes
            'customer_notes'    => 'nullable|string|max:2000',
            'internal_notes'    => 'nullable|string|max:2000',

            // FIX #3: items array — the Edit form sends selected venue/service items
            'items'                     => 'nullable|array',
            'items.*.itemable_type'     => ['required_with:items', 'string', 'in:' . implode(',', array_keys(self::ITEMABLE_TYPE_MAP))],
            'items.*.itemable_id'       => 'required_with:items|integer',
            'items.*.quantity'          => 'required_with:items|integer|min:1',
            'items.*.unit_price'        => 'nullable|numeric|min:0',
        ]);

        DB::beginTransaction();

        $booking->update([
            'venue_id'          => $validated['venue_id'],
            'event_date'        => $validated['event_date'],
            'event_end_date'    => $validated['event_end_date'] ?? null,
            'status'            => $validated['status'],
            'payment_status'    => $validated['payment_status'],
            'total_amount'      => $validated['total_amount'] ?? $booking->total_amount,
            'paid_amount'       => $validated['paid_amount']  ?? $booking->paid_amount,
            'due_date'          => $validated['due_date']     ?? null,
            'customer_notes'    => $validated['customer_notes'] ?? null,
            'internal_notes'    => $validated['internal_notes'] ?? null,
        ]);

        if (isset($validated['items'])) {
            $booking->items()->delete();

            foreach ($validated['items'] as $itemData) {
                $modelClass = self::ITEMABLE_TYPE_MAP[$itemData['itemable_type']];
                $itemable   = $modelClass::findOrFail($itemData['itemable_id']);

                \App\Models\BookingItem::create([
                    'booking_id'    => $booking->id,
                    'itemable_type' => $modelClass,
                    'itemable_id'   => $itemData['itemable_id'],
                    'item_name'     => $itemable->name ?? $itemable->business_name,
                    'description'   => $itemable->description ?? '',
                    'quantity'      => $itemData['quantity'],
                    'unit_price'    => $itemData['unit_price'] ?? ($itemable->base_price ?? $itemable->base_price_per_day),
                ]);
            }
        }

        $booking->calculateTotals();

        // FIX #7: Recompute due_amount after totals are recalculated
        $booking->due_amount = max(0, $booking->total_amount - $booking->paid_amount);
        $booking->save();

        DB::commit();

        Log::info("Booking updated: {$booking->booking_number}", [
            'booking_id' => $booking->id,
            'user_id'    => auth()->id(),
        ]);

        return redirect()->route('bookings.show', $booking->id)
            ->with('success', 'Booking updated successfully');

    } catch (ModelNotFoundException $e) {
        DB::rollBack();
        return redirect()->route('bookings.index')->with('error', 'Booking not found');
    } catch (\Illuminate\Validation\ValidationException $e) {
        DB::rollBack();
        throw $e;
    } catch (Exception $e) {
        DB::rollBack();
        Log::error('Failed to update booking: ' . $e->getMessage(), [
            'booking_id'   => $id,
            'request_data' => $request->except(['_token', '_method']),
        ]);
        return redirect()->back()
            ->with('error', 'Failed to update booking. Please try again.')
            ->withInput();
    }
}


    public function confirm($id)
    {
        try {
            $booking = Booking::findOrFail($id);

            if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('event-organizer')) {
                return redirect()->route('bookings.index')->with('error', 'Unauthorized');
            }

            if ($booking->status !== 'pending') {
                return redirect()->route('bookings.show', $booking->id)
                    ->with('error', 'Only pending bookings can be confirmed');
            }

            DB::beginTransaction();

            $booking->confirm();
            \App\Jobs\ProcessBookingConfirmation::dispatch($booking);

            DB::commit();

            Log::info("Booking confirmed: {$booking->booking_number}", [
                'booking_id' => $booking->id,
                'user_id'    => auth()->id(),
            ]);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking confirmed successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('bookings.index')->with('error', 'Booking not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to confirm booking: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to confirm booking. Please try again.');
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $booking = Booking::findOrFail($id);

            if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('bookings.index')->with('error', 'Unauthorized');
            }

            $validated = $request->validate([
                'cancellation_reason' => 'required|string|max:500',
            ]);

            DB::beginTransaction();

            $booking->cancel($validated['cancellation_reason']);

            DB::commit();

            Log::info("Booking cancelled: {$booking->booking_number}", [
                'booking_id' => $booking->id,
                'user_id'    => auth()->id(),
            ]);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking cancelled successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('bookings.index')->with('error', 'Booking not found');
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

            if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('event-organizer')) {
                return redirect()->route('bookings.index')->with('error', 'Unauthorized');
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
                'user_id'    => auth()->id(),
            ]);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Booking completed successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('bookings.index')->with('error', 'Booking not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to complete booking: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to complete booking. Please try again.');
        }
    }
}