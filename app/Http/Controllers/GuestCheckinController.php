<?php

namespace App\Http\Controllers;

use App\Models\GuestCheckin;
use App\Models\EventGuest;
use App\Models\Event;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Inertia\Inertia;

class GuestCheckinController extends Controller
{
    /**
     * Display a listing of guest check-ins for a specific event.
     */
    public function index(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            // Authorization check
            // if ($event->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
            //     return redirect()->route('events.index')
            //         ->with('error', 'Unauthorized');
            // }

            $search = $request->input('search');
            $status = $request->input('status');
            $method = $request->input('method');
            $dateFrom = $request->input('date_from');
            $dateTo = $request->input('date_to');
            $hasWelcomePack = $request->input('has_welcome_pack');
            $hasBadge = $request->input('has_badge');

            $checkinsQuery = GuestCheckin::with(['guest', 'checkedInBy'])
                ->where('event_id', $eventId);

            // Apply filters
            if ($search) {
                $checkinsQuery->where(function ($query) use ($search) {
                    $query->whereHas('guest', function ($guestQuery) use ($search) {
                        $guestQuery->where('first_name', 'like', '%' . $search . '%')
                            ->orWhere('last_name', 'like', '%' . $search . '%')
                            ->orWhere('email', 'like', '%' . $search . '%');
                    })
                    ->orWhere('check_in_code', 'like', '%' . $search . '%')
                    ->orWhere('badge_number', 'like', '%' . $search . '%');
                });
            }

            if ($status) {
                if ($status === 'active') {
                    $checkinsQuery->whereNull('check_out_time');
                } elseif ($status === 'completed') {
                    $checkinsQuery->whereNotNull('check_out_time');
                }
            }

            if ($method) {
                $checkinsQuery->where('method', $method);
            }

            if ($dateFrom) {
                $checkinsQuery->whereDate('check_in_time', '>=', $dateFrom);
            }

            if ($dateTo) {
                $checkinsQuery->whereDate('check_in_time', '<=', $dateTo);
            }

            if ($hasWelcomePack === 'yes') {
                $checkinsQuery->where('welcome_pack_given', true);
            } elseif ($hasWelcomePack === 'no') {
                $checkinsQuery->where('welcome_pack_given', false);
            }

            if ($hasBadge === 'yes') {
                $checkinsQuery->where('badge_given', true);
            } elseif ($hasBadge === 'no') {
                $checkinsQuery->where('badge_given', false);
            }

            $checkins = $checkinsQuery->orderBy('check_in_time', 'desc')
                ->paginate(50)
                ->withQueryString();

            // Enhanced statistics
            $stats = [
                'total' => GuestCheckin::where('event_id', $eventId)->count(),
                'active' => GuestCheckin::where('event_id', $eventId)->whereNull('check_out_time')->count(),
                'completed' => GuestCheckin::where('event_id', $eventId)->whereNotNull('check_out_time')->count(),
                'vip_checkins' => GuestCheckin::where('event_id', $eventId)
                    ->whereHas('guest', function ($query) {
                        $query->where('is_vip', true);
                    })->count(),
                'avg_duration' => GuestCheckin::where('event_id', $eventId)
                    ->whereNotNull('check_out_time')
                    ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, check_in_time, check_out_time)) as avg_duration'))
                    ->first()->avg_duration ?? 0,
                'total_guests_checked_in' => GuestCheckin::where('event_id', $eventId)->sum('total_guests'),
            ];

            // Method breakdown
            $methods = [
                'qr_code' => GuestCheckin::where('event_id', $eventId)->where('method', 'qr_code')->count(),
                'manual' => GuestCheckin::where('event_id', $eventId)->where('method', 'manual')->count(),
                'facial_recognition' => GuestCheckin::where('event_id', $eventId)->where('method', 'facial_recognition')->count(),
                'rfid' => GuestCheckin::where('event_id', $eventId)->where('method', 'rfid')->count(),
                'mobile_app' => GuestCheckin::where('event_id', $eventId)->where('method', 'mobile_app')->count(),
                'kiosk' => GuestCheckin::where('event_id', $eventId)->where('method', 'kiosk')->count(),
            ];

            // Recent check-ins (last 24 hours)
            $recentCheckins = GuestCheckin::with('guest')
                ->where('event_id', $eventId)
                ->where('check_in_time', '>=', now()->subDay())
                ->orderBy('check_in_time', 'desc')
                ->limit(10)
                ->get();

            // Peak hours
            $peakHours = GuestCheckin::where('event_id', $eventId)
                ->select(DB::raw('HOUR(check_in_time) as hour'), DB::raw('count(*) as count'))
                ->groupBy(DB::raw('HOUR(check_in_time)'))
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            return Inertia::render('Events/Checkins/Index', [
                'event' => $event,
                'checkins' => $checkins,
                'stats' => $stats,
                'methods' => $methods,
                'recentCheckins' => $recentCheckins,
                'peakHours' => $peakHours,
                'filters' => [
                    'search' => $search ?? '',
                    'status' => $status ?? '',
                    'method' => $method ?? '',
                    'date_from' => $dateFrom ?? '',
                    'date_to' => $dateTo ?? '',
                    'has_welcome_pack' => $hasWelcomePack ?? '',
                    'has_badge' => $hasBadge ?? '',
                ]
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('events.index')->with('error', 'Event not found');
        }
    }

    /**
     * Store a newly created guest check-in.
     */
    public function store(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            $validated = $request->validate([
                'guest_id' => 'required|exists:event_guests,id,event_id,' . $eventId,
                'check_in_time' => 'required|date',
                'method' => ['required', Rule::in(GuestCheckin::getMethods())],
                'check_in_code' => 'nullable|string|max:100|unique:guest_checkins,check_in_code',
                'device_id' => 'nullable|string|max:100',
                'location' => 'nullable|string|max:100',
                'total_guests' => 'required|integer|min:1|max:20',
                'checked_in_guests' => 'nullable|array',
                'checked_in_guests.*' => 'string|max:255',
                'welcome_pack_given' => 'boolean',
                'welcome_pack_items' => 'nullable|string|max:255',
                'badge_given' => 'boolean',
                'badge_number' => 'nullable|string|max:50|unique:guest_checkins,badge_number',
                'notes' => 'nullable|string',
                'checked_in_by' => 'nullable|exists:users,id',
            ]);

            DB::beginTransaction();

            // Check if guest is already checked in and not checked out
            $existingCheckin = GuestCheckin::where('guest_id', $validated['guest_id'])
                ->where('event_id', $eventId)
                ->whereNull('check_out_time')
                ->first();

            if ($existingCheckin) {
                return redirect()->back()
                    ->with('error', 'Guest is already checked in.')
                    ->withInput();
            }

            // Generate check-in code if not provided
            if (empty($validated['check_in_code'])) {
                $validated['check_in_code'] = GuestCheckin::generateCheckInCode();
            }

            // Set checked_in_by to current user if not provided
            if (empty($validated['checked_in_by']) && auth()->check()) {
                $validated['checked_in_by'] = auth()->id();
            }

            // Get guest details
            $guest = EventGuest::find($validated['guest_id']);
            
            // Set default checked_in_guests if not provided
            if (empty($validated['checked_in_guests'])) {
                $validated['checked_in_guests'] = [$guest->first_name . ' ' . $guest->last_name];
            }

            $checkin = GuestCheckin::create(array_merge($validated, [
                'event_id' => $eventId,
            ]));

            // Update guest's check-in time
            $guest->update([
                'check_in_time' => $validated['check_in_time'],
                'check_out_time' => null,
            ]);

            DB::commit();

            Log::info("Guest checked in for event: {$event->title}", [
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'checkin_id' => $checkin->id,
                'method' => $validated['method'],
                'user_id' => auth()->id()
            ]);

            return redirect()->route('events.checkins.index', $event->id)
                ->with('success', 'Guest checked in successfully');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to check in guest: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to check in guest. Please try again.')
                ->withInput();
        }
    }

    /**
     * Update the specified guest check-in.
     */
    public function update(Request $request, $eventId, $checkinId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $checkin = GuestCheckin::where('event_id', $eventId)
                ->findOrFail($checkinId);

            $validated = $request->validate([
                'check_out_time' => 'nullable|date',
                'method' => ['sometimes', Rule::in(GuestCheckin::getMethods())],
                'check_in_code' => 'nullable|string|max:100|unique:guest_checkins,check_in_code,' . $checkinId,
                'device_id' => 'nullable|string|max:100',
                'location' => 'nullable|string|max:100',
                'total_guests' => 'sometimes|integer|min:1|max:20',
                'checked_in_guests' => 'nullable|array',
                'checked_in_guests.*' => 'string|max:255',
                'welcome_pack_given' => 'boolean',
                'welcome_pack_items' => 'nullable|string|max:255',
                'badge_given' => 'boolean',
                'badge_number' => 'nullable|string|max:50|unique:guest_checkins,badge_number,' . $checkinId,
                'notes' => 'nullable|string',
                'checked_in_by' => 'nullable|exists:users,id',
            ]);

            DB::beginTransaction();

            $checkin->update($validated);

            // Update guest's check-out time if provided
            if (isset($validated['check_out_time']) && $checkin->guest) {
                $checkin->guest->update([
                    'check_out_time' => $validated['check_out_time'],
                ]);
            }

            DB::commit();

            Log::info("Guest check-in updated for event: {$event->title}", [
                'event_id' => $event->id,
                'checkin_id' => $checkin->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('events.checkins.index', $event->id)
                ->with('success', 'Check-in updated successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('events.checkins.index', $eventId)
                ->with('error', 'Check-in not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to update check-in: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update check-in. Please try again.')
                ->withInput();
        }
    }

    /**
     * Check out a guest.
     */
    public function checkout(Request $request, $eventId, $checkinId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $checkin = GuestCheckin::where('event_id', $eventId)
                ->findOrFail($checkinId);

            // Validate check-out
            $validated = $request->validate([
                'notes' => 'nullable|string',
                'location' => 'nullable|string|max:100',
            ]);

            DB::beginTransaction();

            // Check if already checked out
            if ($checkin->check_out_time) {
                return redirect()->back()
                    ->with('error', 'Guest is already checked out.');
            }

            // Perform check-out
            $checkin->checkOut($validated);

            DB::commit();

            Log::info("Guest checked out for event: {$event->title}", [
                'event_id' => $event->id,
                'checkin_id' => $checkin->id,
                'guest_id' => $checkin->guest_id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('events.checkins.index', $event->id)
                ->with('success', 'Guest checked out successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('events.checkins.index', $eventId)
                ->with('error', 'Check-in not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to check out guest: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to check out guest. Please try again.');
        }
    }

    /**
     * Remove the specified guest check-in.
     */
    public function destroy($eventId, $checkinId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $checkin = GuestCheckin::where('event_id', $eventId)
                ->findOrFail($checkinId);

            DB::beginTransaction();

            // Reset guest's check-in/check-out times if this is their active check-in
            if ($checkin->check_in_time && !$checkin->check_out_time && $checkin->guest) {
                $checkin->guest->update([
                    'check_in_time' => null,
                    'check_out_time' => null,
                ]);
            }

            $checkin->delete();

            DB::commit();

            Log::info("Check-in deleted for event: {$event->title}", [
                'event_id' => $event->id,
                'checkin_id' => $checkinId,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('events.checkins.index', $event->id)
                ->with('success', 'Check-in deleted successfully');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('events.checkins.index', $eventId)
                ->with('error', 'Check-in not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete check-in: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete check-in. Please try again.');
        }
    }

    /**
     * Check in a guest by code (QR code, etc.)
     */
    public function checkinByCode(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            $validated = $request->validate([
                'check_in_code' => 'required|string',
                'method' => ['required', Rule::in(GuestCheckin::getMethods())],
                'device_id' => 'nullable|string|max:100',
                'location' => 'nullable|string|max:100',
                'checked_in_by' => 'nullable|exists:users,id',
            ]);

            DB::beginTransaction();

            // Find guest by check-in code or email or phone
            $guest = EventGuest::where('event_id', $eventId)
                ->where(function ($query) use ($validated) {
                    $query->where('email', $validated['check_in_code'])
                        ->orWhere('phone', $validated['check_in_code'])
                        ->orWhere('invitation_link', 'like', '%' . $validated['check_in_code'] . '%');
                })
                ->first();

            if (!$guest) {
                return redirect()->back()
                    ->with('error', 'Guest not found for the provided code.')
                    ->withInput();
            }

            // Check if already checked in
            $existingCheckin = GuestCheckin::where('guest_id', $guest->id)
                ->where('event_id', $eventId)
                ->whereNull('check_out_time')
                ->first();

            if ($existingCheckin) {
                return redirect()->back()
                    ->with('error', 'Guest is already checked in.')
                    ->withInput();
            }

            // Create check-in record
            $checkinData = [
                'guest_id' => $guest->id,
                'event_id' => $eventId,
                'check_in_time' => now(),
                'method' => $validated['method'],
                'check_in_code' => $validated['check_in_code'],
                'device_id' => $validated['device_id'],
                'location' => $validated['location'],
                'total_guests' => 1 + $guest->plus_ones,
                'checked_in_guests' => [
                    $guest->first_name . ' ' . $guest->last_name,
                ],
                'checked_in_by' => $validated['checked_in_by'] ?? (auth()->check() ? auth()->id() : null),
            ];

            // Add plus ones to checked in guests list
            if ($guest->plus_ones > 0) {
                for ($i = 0; $i < $guest->plus_ones; $i++) {
                    $checkinData['checked_in_guests'][] = 'Guest ' . ($i + 1);
                }
            }

            $checkin = GuestCheckin::create($checkinData);

            // Update guest's check-in time
            $guest->update([
                'check_in_time' => now(),
                'check_out_time' => null,
            ]);

            DB::commit();

            Log::info("Guest checked in by code for event: {$event->title}", [
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'checkin_id' => $checkin->id,
                'method' => $validated['method'],
                'user_id' => auth()->id()
            ]);

            return redirect()->route('events.checkins.index', $event->id)
                ->with('success', 'Guest checked in successfully using code');
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('events.checkins.index', $eventId)
                ->with('error', 'Event not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to check in guest by code: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to check in guest. Please try again.')
                ->withInput();
        }
    }

    /**
     * Bulk check-in for multiple guests
     */
    public function bulkCheckin(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            $validated = $request->validate([
                'guest_ids' => 'required|array',
                'guest_ids.*' => 'exists:event_guests,id,event_id,' . $eventId,
                'method' => ['required', Rule::in(GuestCheckin::getMethods())],
                'location' => 'nullable|string|max:100',
                'checked_in_by' => 'nullable|exists:users,id',
            ]);

            DB::beginTransaction();

            $successful = 0;
            $failed = 0;
            $alreadyCheckedIn = 0;

            foreach ($validated['guest_ids'] as $guestId) {
                try {
                    // Check if already checked in
                    $existingCheckin = GuestCheckin::where('guest_id', $guestId)
                        ->where('event_id', $eventId)
                        ->whereNull('check_out_time')
                        ->first();

                    if ($existingCheckin) {
                        $alreadyCheckedIn++;
                        continue;
                    }

                    $guest = EventGuest::find($guestId);

                    // Create check-in record
                    $checkin = GuestCheckin::create([
                        'guest_id' => $guestId,
                        'event_id' => $eventId,
                        'check_in_time' => now(),
                        'method' => $validated['method'],
                        'check_in_code' => GuestCheckin::generateCheckInCode(),
                        'location' => $validated['location'],
                        'total_guests' => 1 + $guest->plus_ones,
                        'checked_in_guests' => [
                            $guest->first_name . ' ' . $guest->last_name,
                        ],
                        'checked_in_by' => $validated['checked_in_by'] ?? (auth()->check() ? auth()->id() : null),
                    ]);

                    // Update guest's check-in time
                    $guest->update([
                        'check_in_time' => now(),
                        'check_out_time' => null,
                    ]);

                    $successful++;
                } catch (\Exception $e) {
                    $failed++;
                    Log::error("Failed to check in guest {$guestId}: " . $e->getMessage());
                }
            }

            DB::commit();

            Log::info("Bulk check-in completed for event: {$event->title}", [
                'event_id' => $event->id,
                'successful' => $successful,
                'failed' => $failed,
                'already_checked_in' => $alreadyCheckedIn,
                'user_id' => auth()->id()
            ]);

            $message = "Bulk check-in completed. ";
            $message .= "Successfully checked in: {$successful}, ";
            $message .= "Failed: {$failed}, ";
            $message .= "Already checked in: {$alreadyCheckedIn}";

            return redirect()->route('events.checkins.index', $event->id)
                ->with('success', $message);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('events.checkins.index', $eventId)
                ->with('error', 'Event not found');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Failed to perform bulk check-in: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to perform bulk check-in. Please try again.');
        }
    }

    /**
     * Show check-in statistics for an event
     */
    public function statistics($eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            // Basic statistics
            $totalCheckins = GuestCheckin::where('event_id', $eventId)->count();
            $activeCheckins = GuestCheckin::where('event_id', $eventId)
                ->whereNull('check_out_time')
                ->count();
            $completedCheckins = GuestCheckin::where('event_id', $eventId)
                ->whereNotNull('check_out_time')
                ->count();

            // Method distribution
            $methodDistribution = GuestCheckin::where('event_id', $eventId)
                ->select('method', DB::raw('count(*) as count'))
                ->groupBy('method')
                ->get()
                ->pluck('count', 'method')
                ->toArray();

            // Hourly check-ins
            $hourlyCheckins = GuestCheckin::where('event_id', $eventId)
                ->select(DB::raw('HOUR(check_in_time) as hour'), DB::raw('count(*) as count'))
                ->groupBy(DB::raw('HOUR(check_in_time)'))
                ->orderBy('hour')
                ->get();

            // VIP statistics
            $vipCheckins = GuestCheckin::where('event_id', $eventId)
                ->whereHas('guest', function ($query) {
                    $query->where('is_vip', true);
                })
                ->count();

            // Average duration
            $averageDuration = GuestCheckin::where('event_id', $eventId)
                ->whereNotNull('check_out_time')
                ->select(DB::raw('AVG(TIMESTAMPDIFF(MINUTE, check_in_time, check_out_time)) as avg_duration'))
                ->first()
                ->avg_duration ?? 0;

            // Location statistics
            $locationStats = GuestCheckin::where('event_id', $eventId)
                ->whereNotNull('location')
                ->select('location', DB::raw('count(*) as count'))
                ->groupBy('location')
                ->get();

            // Recent check-ins (last 24 hours)
            $recentCheckins = GuestCheckin::with('guest')
                ->where('event_id', $eventId)
                ->where('check_in_time', '>=', now()->subDay())
                ->orderBy('check_in_time', 'desc')
                ->limit(10)
                ->get();

            // Peak hours
            $peakHours = GuestCheckin::where('event_id', $eventId)
                ->select(DB::raw('HOUR(check_in_time) as hour'), DB::raw('count(*) as count'))
                ->groupBy(DB::raw('HOUR(check_in_time)'))
                ->orderByDesc('count')
                ->limit(5)
                ->get();

            return Inertia::render('Events/Checkins/Statistics', [
                'event' => $event,
                'statistics' => [
                    'total_checkins' => $totalCheckins,
                    'active_checkins' => $activeCheckins,
                    'completed_checkins' => $completedCheckins,
                    'method_distribution' => $methodDistribution,
                    'hourly_checkins' => $hourlyCheckins,
                    'vip_checkins' => $vipCheckins,
                    'average_duration_minutes' => round($averageDuration),
                    'location_stats' => $locationStats,
                    'peak_hours' => $peakHours,
                ],
                'recentCheckins' => $recentCheckins,
            ]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('events.index')->with('error', 'Event not found');
        }
    }

    /**
     * Export check-ins to CSV
     */
    public function export(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            $checkins = GuestCheckin::with(['guest', 'checkedInBy'])
                ->where('event_id', $eventId)
                ->orderBy('check_in_time', 'desc')
                ->get();

            $filename = 'checkins-event-' . $event->id . '-' . date('Y-m-d') . '.csv';
            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($checkins) {
                $file = fopen('php://output', 'w');
                
                // Add BOM for UTF-8
                fputs($file, $bom = (chr(0xEF) . chr(0xBB) . chr(0xBF)));
                
                // Headers
                fputcsv($file, [
                    'Guest Name',
                    'Email',
                    'Check-in Time',
                    'Check-out Time',
                    'Method',
                    'Location',
                    'Total Guests',
                    'Welcome Pack Given',
                    'Badge Given',
                    'Badge Number',
                    'Checked In By',
                    'Duration (minutes)',
                ]);

                // Data
                foreach ($checkins as $checkin) {
                    fputcsv($file, [
                        $checkin->guest ? $checkin->guest->full_name : 'N/A',
                        $checkin->guest ? $checkin->guest->email : 'N/A',
                        $checkin->check_in_time ? $checkin->check_in_time->format('Y-m-d H:i:s') : '',
                        $checkin->check_out_time ? $checkin->check_out_time->format('Y-m-d H:i:s') : '',
                        $checkin->method,
                        $checkin->location,
                        $checkin->total_guests,
                        $checkin->welcome_pack_given ? 'Yes' : 'No',
                        $checkin->badge_given ? 'Yes' : 'No',
                        $checkin->badge_number,
                        $checkin->checkedInBy ? $checkin->checkedInBy->name : 'N/A',
                        $checkin->getDurationInMinutes() ?? '',
                    ]);
                }

                fclose($file);
            };

            Log::info("Check-ins exported for event: {$event->title}", [
                'event_id' => $event->id,
                'record_count' => $checkins->count(),
                'user_id' => auth()->id()
            ]);

            return response()->stream($callback, 200, $headers);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return redirect()->route('events.checkins.index', $eventId)
                ->with('error', 'Event not found');
        } catch (\Exception $e) {
            Log::error('Failed to export check-ins: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to export check-ins. Please try again.');
        }
    }
}