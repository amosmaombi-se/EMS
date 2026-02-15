<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Event;
use App\Models\Booking;
use App\Models\EventGuest;
use Exception;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
    public function index()
    {
        try {
            $user = auth()->user();

            if ($user->hasRole('admin')) {
                return $this->adminDashboard();
            } elseif ($user->hasRole('event-organizer')) {
                return $this->organizerDashboard();
            } elseif ($user->hasRole('vendor')) {
                return $this->vendorDashboard();
            } else {
                return $this->customerDashboard();
            }
        } catch (Exception $e) {
            Log::error('Failed to load dashboard: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to load dashboard. Please try again.');
        }
    }

    private function customerDashboard()
    {
        $stats = [
            'total_users' => User::count(),
            'total_events' => Event::count(),
            'total_guests' => EventGuest::count(),
            'total_attending' => EventGuest::where('rsvp_status', 'attending')->count(),
            'total_revenue' => Booking::where('payment_status', 'paid')->sum('total_amount'),
            'pending_bookings' => Booking::where('status', 'pending')->count(),
            'upcoming_events' => Event::where('event_date', '>', now())->count(),
            'active_vendors' => \App\Models\Vendor::where('is_active', true)->count(),
            'pending_reviews' => \App\Models\Review::where('status', 'pending')->count(),
            'events_this_month' => Event::whereMonth('event_date', now()->month)
                ->whereYear('event_date', now()->year)
                ->count(),
        ];

        // Get recent events with guest counts and completion percentages
        $recentEvents = Event::with(['user', 'eventType', 'guests'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($event) {
                $totalGuests = $event->guests()->count();
                $confirmedGuests = $event->guests()->attending()->count();

                $completionPercentage = $totalGuests > 0
                    ? min(100, round(($confirmedGuests / $totalGuests) * 100))
                    : rand(30, 70); // Fallback for demo
    
                $handler = $event->user ? $event->user->name : 'Unassigned';

                // Determine status class for UI
                $statusClass = match ($event->status) {
                    'in_progress' => 'bg-yellow-100 text-yellow-700',
                    'published', 'open' => 'bg-green-100 text-green-700',
                    'completed' => 'bg-blue-100 text-blue-700',
                    'cancelled' => 'bg-red-100 text-red-700',
                    default => 'bg-gray-100 text-gray-700'
                };

                return [
                    'id' => $event->id,
                    'name' => $event->title,
                    'created' => $event->created_at->format('D d M'),
                    'handler' => $handler,
                    'eventDay' => $event->event_date->format('D d M'),
                    'status' => strtoupper(str_replace('_', ' ', $event->status)),
                    'status_class' => $statusClass,
                    'completion' => $completionPercentage,
                    'guests_count' => $totalGuests,
                    'confirmed_guests' => $confirmedGuests,
                    'event_type' => $event->eventType ? $event->eventType->name : 'General',
                    'iconBg' => $this->getEventTypeColor($event->eventType),
                    'cover_image' => $event->cover_image,
                ];
            });

        // Get upcoming events with guest information
        $upcomingEvents = Event::with([
            'user',
            'eventType',
            'guests' => function ($query) {
                $query->attending();
            }
        ])
            ->where('event_date', '>', now())
            ->orderBy('event_date')
            ->limit(5)
            ->get()
            ->map(function ($event) {
                return [
                    'id' => $event->id,
                    'title' => $event->title,
                    'date' => $event->event_date->format('M d, Y'),
                    'time' => $event->start_time ? $event->start_time->format('H:i') : 'TBD',
                    'venue' => $event->venue_name,
                    'confirmed_guests' => $event->guests->count(),
                    'expected_guests' => $event->expected_guests,
                    'status' => $event->status,
                ];
            });

        // Get guest statistics for charts
        $guestStats = [
            'daily' => EventGuest::selectRaw('DATE(created_at) as date, count(*) as total')
                ->where('created_at', '>=', now()->subDays(30))
                ->groupBy('date')
                ->orderBy('date')
                ->get()
                ->pluck('total', 'date'),

            'rsvp_breakdown' => [
                'attending' => EventGuest::where('rsvp_status', 'attending')->count(),
                'not_attending' => EventGuest::where('rsvp_status', 'not_attending')->count(),
                'pending' => EventGuest::where('rsvp_status', 'pending')->count(),
                'maybe' => EventGuest::where('rsvp_status', 'maybe')->count(),
            ],

            'vip_guests' => EventGuest::vip()->count(),
            'guests_with_plus_ones' => EventGuest::withPlusOnes()->count(),
            'guests_with_special_needs' => 0,
        ];

        // Get recent bookings with related data
        $recentBookings = Booking::with(['user', 'event', 'venue'])
            ->latest()
            ->limit(10)
            ->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'booking_number' => $booking->booking_number ?? 'N/A',
                    'customer' => $booking->user ? $booking->user->name : 'Guest',
                    'event' => $booking->event ? $booking->event->title : 'N/A',
                    'amount' => $booking->total_amount,
                    'status' => $booking->status,
                    'payment_status' => $booking->payment_status,
                    'date' => $booking->created_at->format('Y-m-d H:i'),
                ];
            });

        return inertia('Dashboard/Admin', [
            'stats' => $stats,
            'recentEvents' => $recentEvents,
            'upcomingEvents' => $upcomingEvents,
            'recentBookings' => $recentBookings,
            'guestStats' => $guestStats,
        ]);
    }

    /**
     * Helper method to get event type color
     */
    private function getEventTypeColor($eventType)
    {
        if (!$eventType)
            return 'bg-gray-500';

        $colors = [
            'comedy' => 'bg-green-500',
            'graphics' => 'bg-orange-500',
            'tech' => 'bg-blue-500',
            'art' => 'bg-red-500',
            'music' => 'bg-purple-500',
            'conference' => 'bg-indigo-500',
            'workshop' => 'bg-yellow-500',
        ];

        return $colors[strtolower($eventType->name)] ?? 'bg-gray-500';
    }


}

