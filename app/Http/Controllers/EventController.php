<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\EventType;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EventController extends Controller
{
    public function index(Request $request)
    {
        try {
            $search = $request->input('search');
            $status = $request->input('status');
            $eventTypeId = $request->input('event_type_id');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');
            $isPublic = $request->input('is_public');

            $eventsQuery = Event::with(['user', 'eventType', 'eventPackage']);
          

            if ($search) {
                $eventsQuery->where(function ($query) use ($search) {
                    $query->where('title', 'like', '%' . $search . '%')
                        ->orWhere('description', 'like', '%' . $search . '%')
                        ->orWhere('city', 'like', '%' . $search . '%');
                });
            }

            if ($status) {
                $eventsQuery->where('status', $status);
            }

            if ($eventTypeId) {
                $eventsQuery->where('event_type_id', $eventTypeId);
            }

            if ($startDate) {
                $eventsQuery->where('event_date', '>=', $startDate);
            }

            if ($endDate) {
                $eventsQuery->where('event_date', '<=', $endDate);
            }

            if ($isPublic !== null) {
                $eventsQuery->where('is_public', $isPublic);
            }

            $events = $eventsQuery->orderBy('id', 'desc')->paginate(10)->withQueryString();

            $eventTypes = EventType::where('is_active', true)->get();

            return inertia('Events/Index', [
                'events' => $events,
                'eventTypes' => $eventTypes,
                'filters' => [
                    'search' => $search ?? '',
                    'status' => $status ?? '',
                    'event_type_id' => $eventTypeId ?? '',
                    'start_date' => $startDate ?? '',
                    'end_date' => $endDate ?? '',
                    'is_public' => $isPublic ?? '',
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch events: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Failed to fetch events. Please try again.');
        }
    }

    public function create()
    {
        try {
            $eventTypes = EventType::where('is_active', true)->get();

            return inertia('Events/Create', [
                'eventTypes' => $eventTypes,
            ]);
        } catch (Exception $e) {
            Log::error('Failed to load event form: ' . $e->getMessage());
            return redirect()->route('events.index')
                ->with('error', 'Failed to load event form. Please try again.');
        }
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'event_type_id' => 'required|exists:event_types,id',
                'title' => 'required|string|max:255',
                'description' => 'nullable|string',
                'event_date' => 'required|date|after:today',
                'event_end_date' => 'nullable|date|after_or_equal:event_date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i|after:start_time',
                'venue_name' => 'nullable|string|max:255',
                'venue_address' => 'nullable|string',
                'city' => 'required|string|max:100',
                'postal_code' => 'nullable|string|max:20',
                'expected_guests' => 'required|integer|min:1',
                'estimated_budget' => 'nullable|numeric|min:0',
                'status' => ['sometimes', Rule::in(['draft', 'planning', 'confirmed', 'ongoing', 'completed', 'cancelled'])],
                'is_public' => 'sometimes|boolean',
            ]);

            DB::beginTransaction();

            $validated['user_id'] = auth()->id();
            $validated['status'] = $validated['status'] ?? 'draft';
            $validated['confirmed_guests'] = 0;
            $validated['state'] = 'default';
            $validated['country'] = 'Tanzania';
            $validated['actual_cost'] = 0;
            $validated['estimated_budget'] =  $validated['estimated_budget']  ?? 0;

            $event = Event::create($validated);

            // Create default checklist from event type
            $eventType = EventType::find($validated['event_type_id']);
            if ($eventType && $eventType->default_checklist) {
                $checklist = $event->checklists()->create([
                    'title' => 'Main Checklist',
                    'description' => 'Default checklist for ' . $eventType->name,
                    'sort_order' => 1,
                ]);

                foreach ($eventType->default_checklist as $index => $taskTitle) {
                    $checklist->tasks()->create([
                        'event_id' => $event->id,
                        'title' => $taskTitle,
                        'status' => 'pending',
                        'priority' => 'medium',
                        'sort_order' => $index + 1,
                    ]);
                }
            }

            DB::commit();

            Log::info("Event created: {$event->title}", ['event_id' => $event->id, 'user_id' => auth()->id()]);

            return redirect()->route('events.show', $event->id)
                ->with('success', 'Event created successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to create event: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to create event. Please try again.')
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $event = Event::with([
                'user',
                'eventType',
                'eventPackage',
                'schedules',
                'checklists.tasks.assignedUser',
                'guests',
                'bookings.items',
                'bookings.payments'
            ])->findOrFail($id);

            // Authorization check
            if ($event->user_id !== auth()->id() && !auth()->user()->hasRole('admin') && !$event->is_public) {
                return redirect()->route('events.index')
                    ->with('error', 'Unauthorized');
            }

            // Calculate stats
            $stats = [
                'total_guests' => $event->guests->count(),
                'confirmed_guests' => $event->guests->where('rsvp_status', 'attending')->count(),
                'pending_rsvp' => $event->guests->where('rsvp_status', 'pending')->count(),
                'total_tasks' => $event->tasks->count(),
                'completed_tasks' => $event->tasks->where('status', 'completed')->count(),
                'budget_used_percentage' => $event->estimated_budget > 0
                    ? ($event->actual_cost / $event->estimated_budget) * 100
                    : 0,
            ];

            return inertia('Events/Show', [
                'event' => $event,
                'stats' => $stats
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('events.index')
                ->with('error', 'Event not found');
        } catch (Exception $e) {
            Log::error('Failed to fetch event: ' . $e->getMessage());
            return redirect()->route('events.index')
                ->with('error', 'Failed to fetch event details. Please try again.');
        }
    }

    public function edit($id)
    {
        try {
            $event = Event::findOrFail($id);

            if ($event->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('events.index')
                    ->with('error', 'Unauthorized');
            }

            $eventTypes = EventType::where('is_active', true)->get();

            return inertia('Events/Edit', [
                'event' => $event,
                'eventTypes' => $eventTypes,
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('events.index')
                ->with('error', 'Event not found');
        } catch (Exception $e) {
            Log::error('Failed to load event edit form: ' . $e->getMessage());
            return redirect()->route('events.index')->with('error', 'Failed to load event. Please try again.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            $request->merge([
                'start_time' => $request->start_time ? $this->formatTime($request->start_time) : null,
                'end_time' => $request->end_time ? $this->formatTime($request->end_time) : null,
            ]);

            $validated = $request->validate([
                'title' => 'sometimes|required|string|max:255',
                'description' => 'nullable|string',
                'event_date' => 'sometimes|required|date',
                'event_end_date' => 'nullable|date|after_or_equal:event_date',
                'start_time' => 'nullable|date_format:H:i',
                'end_time' => 'nullable|date_format:H:i|after:start_time',
                'venue_name' => 'nullable|string|max:255',
                'venue_address' => 'nullable|string',
                'city' => 'sometimes|required|string|max:100',
                'expected_guests' => 'sometimes|required|integer|min:1',
                'estimated_budget' => 'sometimes|nullable|numeric|min:0',
                'actual_cost' => 'nullable|numeric|min:0',
                'status' => ['sometimes', Rule::in(['draft', 'planning', 'confirmed', 'ongoing', 'completed', 'cancelled'])],
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();
            

            $event->update($validated);

            DB::commit();

            Log::info("Event updated: {$event->title}", ['event_id' => $event->id, 'user_id' => auth()->id()]);

            return redirect()->route('events.show', $event->id)->with('success', 'Event updated successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('events.index')->with('error', 'Event not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update event: ' . $e->getMessage(), [
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update event. ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Format time input to H:i format
     */
    private function formatTime($time)
    {
        if (empty($time)) {
            return null;
        }

        try {
            // If it's already in H:i format, return as is
            if (preg_match('/^\d{2}:\d{2}$/', $time)) {
                return $time;
            }

            // Try to parse various time formats
            $parsed = date_parse($time);
            if ($parsed['error_count'] === 0) {
                return sprintf('%02d:%02d', $parsed['hour'], $parsed['minute']);
            }

            // Try Carbon parsing
            return \Carbon\Carbon::parse($time)->format('H:i');
        } catch (\Exception $e) {
            Log::warning('Failed to format time', ['time' => $time, 'error' => $e->getMessage()]);
            return $time; // Let validation handle it
        }
    }

    public function destroy($id)
    {
        try {
            $event = Event::findOrFail($id);

            // Authorization check
            if ($event->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('events.index')
                    ->with('error', 'Unauthorized');
            }

            // Check for bookings
            if ($event->bookings()->whereIn('status', ['confirmed', 'in_progress'])->exists()) {
                return redirect()->route('events.show', $event->id)
                    ->with('error', 'Cannot delete event with active bookings');
            }

            DB::beginTransaction();

            $eventTitle = $event->title;
            $event->delete();

            DB::commit();

            Log::info("Event deleted: {$eventTitle}", ['user_id' => auth()->id()]);

            return redirect()->route('events.index')
                ->with('success', 'Event deleted successfully');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('events.index')
                ->with('error', 'Event not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to delete event: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to delete event. Please try again.');
        }
    }

    public function cancel(Request $request, $id)
    {
        try {
            $event = Event::findOrFail($id);

            // Authorization check
            if ($event->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('events.index')
                    ->with('error', 'Unauthorized');
            }

            $validated = $request->validate([
                'cancellation_reason' => 'required|string|max:500',
            ]);

            DB::beginTransaction();

            $event->update([
                'status' => 'cancelled',
                'cancelled_at' => now(),
                'cancellation_reason' => $validated['cancellation_reason'],
            ]);

            // Cancel associated bookings
            $event->bookings()->whereIn('status', ['pending', 'confirmed'])->each(function ($booking) use ($validated) {
                $booking->cancel($validated['cancellation_reason']);
            });

            DB::commit();

            Log::info("Event cancelled: {$event->title}", ['event_id' => $event->id, 'user_id' => auth()->id()]);

            return redirect()->route('events.show', $event->id)
                ->with('success', 'Event cancelled successfully');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to cancel event: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to cancel event. Please try again.')
                ->withInput();
        }
    }
}


