<?php

namespace App\Http\Controllers;

use SimpleSoftwareIO\QrCode\Facades\QrCode;
use App\Models\Event;
use App\Models\EventGuest;
use Illuminate\Validation\Rule;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str;
use App\Services\EmailService;



class EventGuestController extends Controller
{

    public function __construct(
        private readonly EmailService $emailService
    ) {
    }
    public function index(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            $search = $request->input('search');
            $rsvpStatus = $request->input('rsvp_status');
            $category = $request->input('category');
            $guestType = $request->input('guest_type');
            $hasSpecialNeeds = $request->input('has_special_needs');
            $invitationStatus = $request->input('invitation_status');

            $guestsQuery = $event->guests();


            $perPage = $request->get('per_page', 15);
    
             $guests = $guestsQuery->paginate($perPage)->withQueryString();

            if ($search) {
                $guestsQuery->where(function ($query) use ($search) {
                    $query->where('first_name', 'like', '%' . $search . '%')
                        ->orWhere('last_name', 'like', '%' . $search . '%')
                        ->orWhere('email', 'like', '%' . $search . '%')
                        ->orWhere('phone', 'like', '%' . $search . '%');
                });
            }

            if ($rsvpStatus) {
                $guestsQuery->where('rsvp_status', $rsvpStatus);
            }

            if ($category) {
                $guestsQuery->where('category', $category);
            }

            if ($guestType) {
                $guestsQuery->where('guest_type', $guestType);
            }

            if ($hasSpecialNeeds === 'yes') {
                $guestsQuery->where(function ($query) {
                    $query->whereNotNull('dietary_preference')
                        ->orWhereNotNull('special_requirements')
                        ->orWhereNotNull('allergies')
                        ->orWhereNotNull('accessibility_needs');
                });
            }

            if ($invitationStatus) {
                switch ($invitationStatus) {
                    case 'not_sent':
                        $guestsQuery->where('invitation_sent', false);
                        break;
                    case 'sent_pending':
                        $guestsQuery->where('invitation_sent', true)
                            ->whereNull('rsvp_responded_at');
                        break;
                    case 'responded':
                        $guestsQuery->whereNotNull('rsvp_responded_at');
                        break;
                }
            }

            $guests = $guestsQuery->orderBy('id', 'desc')
                ->orderBy('category')
                ->orderBy('last_name')
                ->paginate(10)
                ->withQueryString();

            // Enhanced statistics
            $stats = [
                'total' => $event->guests->count(),
                'attending' => $event->guests->where('rsvp_status', 'attending')->count(),
                'not_attending' => $event->guests->where('rsvp_status', 'not_attending')->count(),
                'pending' => $event->guests->where('rsvp_status', 'pending')->count(),
                'maybe' => $event->guests->where('rsvp_status', 'maybe')->count(),
                'vip' => $event->guests->where('is_vip', true)->count(),
                'with_plus_ones' => $event->guests->where('plus_ones', '>', 0)->count(),
                'total_guests_count' => $event->guests->sum(function ($guest) {
                    return 1 + $guest->plus_ones;
                }),
                'invitations_sent' => $event->guests->where('invitation_sent', true)->count(),
                'rsvp_responded' => $event->guests->whereNotNull('rsvp_responded_at')->count(),
            ];

            // Category breakdown
            $categories = [
                'vip' => $event->guests->where('category', 'vip')->count(),
                'family' => $event->guests->where('category', 'family')->count(),
                'friends' => $event->guests->where('category', 'friends')->count(),
                'colleagues' => $event->guests->where('category', 'colleagues')->count(),
                'business' => $event->guests->where('category', 'business')->count(),
                'media' => $event->guests->where('category', 'media')->count(),
                'sponsors' => $event->guests->where('category', 'sponsors')->count(),
                'other' => $event->guests->where('category', 'other')->count(),
            ];

            return inertia('Events/Guests/Index', [
                'event' => $event->load('eventType'),
                'guests' => $guests,
                'stats' => $stats,
                'categories' => $categories,
                'filters' => [
                    'search' => $search ?? '',
                    'rsvp_status' => $rsvpStatus ?? '',
                    'category' => $category ?? '',
                    'guest_type' => $guestType ?? '',
                    'has_special_needs' => $hasSpecialNeeds ?? '',
                    'invitation_status' => $invitationStatus ?? '',
                ]
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('events.index')->with('error', 'Event not found');
        }
    }

    public function create($eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            return inertia('Events/Guests/Create', [
                'event' => $event,
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('events.guests.index', $eventId)
                ->with('error', 'Event not found');
        }
    }

    public function bulkStore(Request $request, $eventId)
    {
        try {
            $event = Event::findOrFail($eventId);

            // Validate the request
            $validated = $request->validate([
                'guests' => 'required|array|min:1',
                'guests.*.first_name' => 'required|string|max:100',
                'guests.*.last_name' => 'required|string|max:100',
                'guests.*.email' => 'nullable|email|max:255',
                'guests.*.phone' => 'nullable|string|max:20',
                'guests.*.category' => ['required', Rule::in(['vip', 'family', 'friends', 'colleagues', 'business', 'media', 'sponsors', 'other'])],
                'guests.*.guest_type' => ['nullable', Rule::in(['primary', 'plus_one', 'child', 'vendor', 'staff', 'speaker', 'performer'])],
                'guests.*.rsvp_status' => ['required', Rule::in(['pending', 'attending', 'not_attending', 'maybe'])],
                'guests.*.plus_ones' => 'nullable|integer|min:0|max:10',
                'guests.*.plus_one_allowed' => 'boolean',
                'guests.*.dietary_preference' => 'nullable|string|max:100',
                'guests.*.allergies' => 'nullable|string|max:500',
                'guests.*.special_requirements' => 'nullable|string',
                'guests.*.accessibility_needs' => 'nullable|string|max:500',
                'guests.*.accommodation_needs' => 'nullable|string|max:500',
                'guests.*.transportation_needs' => 'nullable|string|max:500',
                'guests.*.is_vip' => 'boolean',
                'guests.*.language_preference' => 'nullable|string|max:50',
                'guests.*.notes' => 'nullable|string',
                'send_invitations' => 'boolean',
                'invitation_method' => ['nullable', 'required_if:send_invitations,true', Rule::in(['email', 'sms', 'whatsapp'])],
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $createdGuests = [];
            $attendingCount = 0;
            $invitationsSent = 0;
            $duplicateEmails = [];
            $validationErrors = [];

            // Check for duplicate emails within this batch
            $emailOccurrences = [];
            foreach ($validated['guests'] as $guestData) {
                if (!empty($guestData['email'])) {
                    $email = strtolower(trim($guestData['email']));
                    if (isset($emailOccurrences[$email])) {
                        $emailOccurrences[$email]++;
                    } else {
                        $emailOccurrences[$email] = 1;
                    }
                }
            }

            $existingEmails = EventGuest::where('event_id', $eventId)
                ->whereNotNull('email')
                ->whereIn('email', array_filter(array_column($validated['guests'], 'email')))
                ->pluck('email')
                ->map(fn($email) => strtolower(trim($email)))
                ->toArray();

            foreach ($validated['guests'] as $index => $guestData) {
                $email = !empty($guestData['email']) ? strtolower(trim($guestData['email'])) : null;

                if ($email && isset($emailOccurrences[$email]) && $emailOccurrences[$email] > 1) {
                    $validationErrors["guests.{$index}.email"] = ['This email is duplicated in this batch. Each guest must have a unique email.'];
                    continue; // Skip this guest but continue with others
                }

                if ($email && in_array($email, $existingEmails)) {
                    $duplicateEmails[] = [
                        'email' => $email,
                        'index' => $index,
                        'name' => $guestData['first_name'] . ' ' . $guestData['last_name']
                    ];
                    continue; // Skip this guest
                }


                // Create guest
                $guest = $event->guests()->create([
                    'first_name' => $guestData['first_name'],
                    'last_name' => $guestData['last_name'],
                    'email' => $email,
                    'phone' => $this->formatTanzanianNumber($guestData['phone'] ?? null),
                    'category' => $guestData['category'],
                    'guest_type' => $guestData['guest_type'] ?? 'primary',
                    'rsvp_status' => $guestData['rsvp_status'],
                    'plus_ones' => $guestData['plus_ones'] ?? 0,
                    'plus_one_allowed' => $guestData['plus_one_allowed'] ?? false,
                    'dietary_preference' => $guestData['dietary_preference'] ?? null,
                    'allergies' => $guestData['allergies'] ?? null,
                    'special_requirements' => $guestData['special_requirements'] ?? null,
                    'accessibility_needs' => $guestData['accessibility_needs'] ?? null,
                    'accommodation_needs' => $guestData['accommodation_needs'] ?? null,
                    'transportation_needs' => $guestData['transportation_needs'] ?? null,
                    'is_vip' => $guestData['is_vip'] ?? false,
                    'language_preference' => $guestData['language_preference'] ?? null,
                    'notes' => $guestData['notes'] ?? ($validated['notes'] ?? null),
                    'rsvp_responded_at' => $guestData['rsvp_status'] !== 'pending' ? now() : null,
                ]);

                $createdGuests[] = $guest;

                if ($guestData['rsvp_status'] === 'attending') {
                    $attendingCount++;
                }

                // Send invitation if requested
                if (($validated['send_invitations'] ?? false) && $guest->email && ($validated['invitation_method'] ?? 'email') === 'email') {
                    $customMessage = $validated['invitation_message'] ?? $validated['notes'] ?? null;
                    $priority = $validated['invitation_priority'] ?? 'medium';

                    if ($this->sendSingleInvitation($guest, $event, $customMessage,$imagePath=null, $priority)) {
                        $invitationsSent++;
                        Log::info("Invitation sent successfully", [
                            'guest_id' => $guest->id,
                            'email' => $guest->email,
                            'event_id' => $event->id,
                        ]);
                    } else {
                        Log::warning("Failed to send invitation", [
                            'guest_id' => $guest->id,
                            'email' => $guest->email,
                            'event_id' => $event->id,
                        ]);
                    }
                }

            }

            if ($attendingCount > 0) {
                $event->increment('confirmed_guests', $attendingCount);
            }

            DB::commit();

            Log::info("Bulk guests created for event: {$event->title}", [
                'event_id' => $event->id,
                'total_requested' => count($validated['guests']),
                'created_count' => count($createdGuests),
                'skipped_duplicates' => count($duplicateEmails),
                'attending_count' => $attendingCount,
                'invitations_sent' => $invitationsSent,
                'guest_ids' => array_column($createdGuests, 'id'),
                'user_id' => auth()->id()
            ]);

            // Prepare success message
            $successMessage = count($createdGuests) . ' guest(s) added successfully.';

            if (count($duplicateEmails) > 0) {
                $successMessage .= ' Skipped ' . count($duplicateEmails) . ' guest(s) with duplicate emails.';
            }

            if ($invitationsSent > 0) {
                $successMessage .= ' ' . $invitationsSent . ' invitation(s) sent.';
            }

            if (($validated['send_invitations'] ?? false) && $invitationsSent < count($createdGuests)) {
                $emailMissing = count($createdGuests) - $invitationsSent;
                $successMessage .= ' ' . $emailMissing . ' guest(s) not sent invitations (no email address).';
            }

            // Check if request is AJAX/JSON
            if ($request->expectsJson() || $request->is('api/*') || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => true,
                    'message' => $successMessage,
                    'created_count' => count($createdGuests),
                    'skipped_count' => count($duplicateEmails),
                    'skipped_details' => $duplicateEmails,
                    'invitations_sent' => $invitationsSent,
                    'guest_ids' => array_column($createdGuests, 'id')
                ]);
            }

            // For regular web requests
            return redirect()->route('events.guests.index', $event->id)
                ->with('success', $successMessage);

        } catch (ValidationException $e) {
            DB::rollBack();

            // Check if request is AJAX/JSON
            if ($request->expectsJson() || $request->is('api/*') || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Invalid request data')
                ->withInput();

        } catch (ModelNotFoundException $e) {
            DB::rollBack();

            $errorMessage = 'Event not found';

            // Check if request is AJAX/JSON
            if ($request->expectsJson() || $request->is('api/*') || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 404);
            }

            return redirect()->route('events.index')
                ->with('error', $errorMessage);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk create guests: ' . $e->getMessage(), [
                'event_id' => $eventId,
                'guests_count' => count($request->guests ?? []),
                'user_id' => auth()->id()
            ]);

            $errorMessage = 'Failed to add guests. Please try again.';

            // Check if request is AJAX/JSON
            if ($request->expectsJson() || $request->is('api/*') || $request->header('X-Requested-With') === 'XMLHttpRequest') {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => config('app.debug') ? $e->getMessage() : null
                ], 500);
            }

            return redirect()->back()
                ->with('error', $errorMessage)
                ->withInput();
        }
    }


    public function edit(Request $request, $eventId, $id)
    {
        try {
            $event = Event::findOrFail($eventId);
            $guest = EventGuest::where('event_id', $eventId)->findOrFail($id);

            // Return Inertia response with guest data
            return inertia('Events/Guests/Edit', [
                'event' => [
                    'id' => $event->id,
                    'title' => $event->title,
                ],
                'guest' => [
                    'id' => $guest->id,
                    'first_name' => $guest->first_name,
                    'last_name' => $guest->last_name,
                    'email' => $guest->email,
                    'phone' => $this->formatTanzanianNumber($guest->phone),
                    'category' => $guest->category,
                    'guest_type' => $guest->guest_type,
                    'rsvp_status' => $guest->rsvp_status,
                    'plus_ones' => $guest->plus_ones,
                    'plus_one_allowed' => $guest->plus_one_allowed,
                    'dietary_preference' => $guest->dietary_preference,
                    'allergies' => $guest->allergies,
                    'special_requirements' => $guest->special_requirements,
                    'accessibility_needs' => $guest->accessibility_needs,
                    'accommodation_needs' => $guest->accommodation_needs,
                    'transportation_needs' => $guest->transportation_needs,
                    'is_vip' => $guest->is_vip,
                    'language_preference' => $guest->language_preference,
                    'notes' => $guest->notes,
                    'invitation_sent' => $guest->invitation_sent,
                    'invitation_method' => $guest->invitation_method,
                    'invitation_sent_at' => $guest->invitation_sent_at,
                    'invitation_link' => $guest->invitation_link,
                    'rsvp_responded_at' => $guest->rsvp_responded_at,
                    'rsvp_last_reminded_at' => $guest->rsvp_last_reminded_at,
                    'welcome_pack_sent' => $guest->welcome_pack_sent,
                    'check_in_time' => $guest->check_in_time,
                    'check_out_time' => $guest->check_out_time,
                    'created_at' => $guest->created_at,
                    'updated_at' => $guest->updated_at,
                ],
            ]);

        } catch (ModelNotFoundException $e) {
            return redirect()->route('events.guests.index', $eventId)
                ->with('error', 'Guest not found');
        }
    }


    public function update(Request $request, $eventId, $id)
    {
        try {
            $event = Event::findOrFail($eventId);
            $guest = EventGuest::where('event_id', $eventId)->findOrFail($id);

            // Validate request
            $validated = $request->validate([
                'first_name' => 'required|string|max:100',
                'last_name' => 'required|string|max:100',
                'email' => [
                    'nullable',
                    'email',
                    'max:255',
                    function ($attribute, $value, $fail) use ($eventId, $guest) {
                        if ($value) {
                            $exists = EventGuest::where('event_id', $eventId)
                                ->where('email', $value)
                                ->where('id', '!=', $guest->id)
                                ->exists();

                            if ($exists) {
                                $fail('This email is already in use for another guest in this event.');
                            }
                        }
                    }
                ],
                'phone' => 'nullable|string|max:20',
                'category' => ['required', Rule::in(['vip', 'family', 'friends', 'colleagues', 'business', 'media', 'sponsors', 'other'])],
                'guest_type' => ['nullable', Rule::in(['primary', 'plus_one', 'child', 'vendor', 'staff', 'speaker', 'performer', 'sponsor'])],
                'rsvp_status' => ['required', Rule::in(['pending', 'attending', 'not_attending', 'maybe'])],
                'plus_ones' => 'nullable|integer|min:0|max:10',
                'plus_one_allowed' => 'boolean',
                'dietary_preference' => 'nullable|string|max:100',
                'allergies' => 'nullable|string|max:500',
                'special_requirements' => 'nullable|string',
                'accessibility_needs' => 'nullable|string|max:500',
                'accommodation_needs' => 'nullable|string|max:500',
                'transportation_needs' => 'nullable|string|max:500',
                'is_vip' => 'boolean',
                'language_preference' => 'nullable|string|max:50',
                'notes' => 'nullable|string',
            ]);

            DB::beginTransaction();

            $oldStatus = $guest->rsvp_status;
            $newStatus = $validated['rsvp_status'];

            // Update guest
            $guest->update([
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'] ?? null,
                'phone' => $this->formatTanzanianNumber($validated['phone'] ?? null),
                'category' => $validated['category'],
                'guest_type' => $validated['guest_type'] ?? 'primary',
                'rsvp_status' => $newStatus,
                'plus_ones' => $validated['plus_ones'] ?? 0,
                'plus_one_allowed' => $validated['plus_one_allowed'] ?? false,
                'dietary_preference' => $validated['dietary_preference'] ?? null,
                'allergies' => $validated['allergies'] ?? null,
                'special_requirements' => $validated['special_requirements'] ?? null,
                'accessibility_needs' => $validated['accessibility_needs'] ?? null,
                'accommodation_needs' => $validated['accommodation_needs'] ?? null,
                'transportation_needs' => $validated['transportation_needs'] ?? null,
                'is_vip' => $validated['is_vip'] ?? false,
                'language_preference' => $validated['language_preference'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'rsvp_responded_at' => $newStatus !== 'pending' ? now() : $guest->rsvp_responded_at,
            ]);

            // Update event confirmed guests count
            if ($oldStatus !== 'attending' && $newStatus === 'attending') {
                $event->increment('confirmed_guests');
            } elseif ($oldStatus === 'attending' && $newStatus !== 'attending') {
                $event->decrement('confirmed_guests');
            }

            DB::commit();
            return redirect()->route('events.guests.edit', [$event->id, $guest->id])->with('success', 'Guest updated successfully');
        } catch (ValidationException $e) {
            DB::rollBack();
            throw $e; // Let Inertia handle validation errors

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update guest: ' . $e->getMessage(), [
                'event_id' => $eventId,
                'guest_id' => $id,
            ]);

            return redirect()->back()
                ->with('error', 'Failed to update guest. Please try again.')
                ->withInput();
        }
    }

    // Send single invitation
    private function sendSingleInvitation(EventGuest $guest, Event $event, ?string $customMessage = null, ?string $imagePath, string $priority = 'high')
    {
        try {
            $invitationToken = Str::random(64);

            $invitationUrl = route('events.invitation.show', [
                'event' => $event->id,
                'token' => $invitationToken
            ]);

            $qrCodeData = json_encode([
                'event_id' => $event->id,
                'event_title' => $event->title,
                'guest_id' => $guest->id,
                'guest_name' => $guest->first_name . ' ' . $guest->last_name,
                'guest_email' => $guest->email,
                'token' => $invitationToken,
                'invitation_url' => $invitationUrl,
                'timestamp' => now()->timestamp
            ]);

            $notificationData = [
                'title' => "Invitation to {$event->title}",
                'message' => $customMessage ?? "You're invited to {$event->title} on {$event->event_date->format('F j, Y')} at {$event->venue_address}. Please RSVP using the link below.",
                'email' => $guest->email,
                'subject' => "You're Invited: {$event->title}",
                'priority' => $priority,
                'event_data' => [
                    'event_id' => $event->id,
                    'guest_id' => $guest->id,
                    'event_title' => $event->title,
                    'event_date' => $event->event_date,
                    'event_venue_address' => $event->venue_address,
                    'guest_name' => $guest->full_name,
                    'guest_email' => $guest->email,
                    'invitation_url' => $invitationUrl,
                    'qr_code_data' => $qrCodeData,
                    'custom_message' => $customMessage,
                    'invitation_token' => $invitationToken,
                ]
            ];

            DB::beginTransaction();

            $notification = $this->emailService->sendNotification($notificationData);

            $guest->update([
                'invitation_sent' => true,
                'invitation_sent_at' => now(),
                'invitation_method' => 'email',
                'invitation_token' => $invitationToken,
                'invitation_link' => $invitationUrl,
            ]);

            DB::commit();

            Log::info('Invitation queued and guest updated', [
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'email' => $guest->email,
                'notification_id' => $notification->id,
                'priority' => $priority,
            ]);

            return true;

        } catch (Exception $e) {
            DB::rollBack();

            Log::error('Failed to send invitation', [
                'event_id' => $event->id,
                'guest_id' => $guest->id,
                'email' => $guest->email,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    // Send reminder
    private function sendReminder(EventGuest $guest)
    {
        try {
            // Send reminder email
            // Mail::to($guest->email)->send(new RsvpReminder($guest));

            $guest->update(['rsvp_last_reminded_at' => now()]);

            Log::info("Reminder sent to: {$guest->email}", [
                'guest_id' => $guest->id,
                'event_id' => $guest->event_id,
            ]);

            return true;
        } catch (Exception $e) {
            Log::error('Failed to send reminder: ' . $e->getMessage());
            return false;
        }
    }

    // Delete guest
    public function destroy($eventId, $guestId)
    {
        try {
            $event = Event::findOrFail($eventId);
            $guest = EventGuest::where('event_id', $eventId)->where('id', $guestId)->firstOrFail();

            DB::beginTransaction();

            $guestName = $guest->first_name . ' ' . $guest->last_name;

            // Update confirmed guests count if guest was attending
            if ($guest->rsvp_status === 'attending') {
                $event->decrement('confirmed_guests');
            }

            $guest->update(['email' => 'deleted_' . time() . '_' . $guest->email]); // Soft delete
            $guest->delete();

            DB::commit();

            Log::info("Guest removed from event: {$event->title}", [
                'event_id' => $event->id,
                'guest_name' => $guestName,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('events.guests.index', $event->id)->with('success', 'Guest removed successfully');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('events.guests.index', $eventId)->with('error', 'Guest not found');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to remove guest: ' . $e->getMessage(), [
                'event_id' => $eventId,
                'guest_id' => $guestId,
                'user_id' => auth()->id()
            ]);
            return redirect()->back()->with('error', 'Failed to remove guest. Please try again.');
        }
    }


    public function bulkDestroy(Request $request, $eventId)
    {
        try {
            $request->validate([
                'guest_ids' => 'required|array|min:1',
                'guest_ids.*' => 'required|integer|exists:event_guests,id'
            ]);

            $event = Event::findOrFail($eventId);
            DB::beginTransaction();

            $guests = EventGuest::where('event_id', $eventId)
                ->whereIn('id', $request->guest_ids)
                ->get();

            if ($guests->isEmpty()) {
                DB::rollBack();
                return redirect()->route('events.guests.index', $event->id)
                    ->with('error', 'No guests found to delete');
            }

            $attendingCount = $guests->where('rsvp_status', 'attending')->count();

            $guestNames = $guests->map(function ($guest) {
                return $guest->first_name . ' ' . $guest->last_name;
            })->toArray();

            $deletedCount = $guests->count();

            // Delete all selected guests
            EventGuest::where('event_id', $eventId)
                ->whereIn('id', $request->guest_ids)
                ->delete();

            // Update confirmed guests count
            if ($attendingCount > 0) {
                $event->decrement('confirmed_guests', $attendingCount);
            }

            DB::commit();

            Log::info("Bulk guest deletion from event: {$event->title}", [
                'event_id' => $event->id,
                'deleted_count' => $deletedCount,
                'guest_names' => $guestNames,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('events.guests.index', $event->id)
                ->with('success', "{$deletedCount} guest(s) removed successfully");

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Invalid guest selection');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('events.index')
                ->with('error', 'Event not found');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk delete guests: ' . $e->getMessage(), [
                'event_id' => $eventId,
                'guest_ids' => $request->guest_ids ?? [],
                'user_id' => auth()->id()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to remove guests. Please try again.');
        }
    }


    // Update RSVP
    public function updateRsvp(Request $request, $eventId, $guestId)
    {
        try {
            $guest = EventGuest::where('event_id', $eventId)
                ->where('id', $guestId)
                ->firstOrFail();

            $validated = $request->validate([
                'rsvp_status' => ['required', Rule::in(['attending', 'not_attending', 'maybe'])],
                'plus_ones' => 'nullable|integer|min:0|max:' . ($guest->plus_ones ?? 0),
            ]);

            DB::beginTransaction();

            $oldStatus = $guest->rsvp_status;

            $guest->update([
                'rsvp_status' => $validated['rsvp_status'],
                'plus_ones' => $validated['plus_ones'] ?? 0,
                'rsvp_responded_at' => now(),
            ]);

            // Update event confirmed guests count
            $event = $guest->event;
            if ($validated['rsvp_status'] === 'attending' && $oldStatus !== 'attending') {
                $event->increment('confirmed_guests');
            } elseif ($oldStatus === 'attending' && $validated['rsvp_status'] !== 'attending') {
                $event->decrement('confirmed_guests');
            }

            DB::commit();

            Log::info("RSVP updated for guest", [
                'event_id' => $eventId,
                'guest_id' => $guestId,
                'old_status' => $oldStatus,
                'new_status' => $validated['rsvp_status']
            ]);

            return redirect()->back()->with('success', 'RSVP updated successfully');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('events.index')
                ->with('error', 'Guest not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to update RSVP: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to update RSVP. Please try again.');
        }
    }

    public function bulkUpdate(Request $request, $eventId)
    {
        try {
            $validated = $request->validate([
                'guest_ids' => 'required|array|min:1',
                'guest_ids.*' => 'required|integer|exists:event_guests,id',
                'rsvp_status' => ['required', Rule::in(['attending', 'not_attending', 'maybe', 'pending'])],
            ]);

            $event = Event::findOrFail($eventId);

            DB::beginTransaction();

            $guests = EventGuest::where('event_id', $eventId)->whereIn('id', $validated['guest_ids'])->get();

            if ($guests->isEmpty()) {
                DB::rollBack();
                return redirect()->route('events.guests.index', $event->id)->with('error', 'No guests found to update');
            }

            $updatedCount = 0;
            $attendingCountChange = 0;

            foreach ($guests as $guest) {
                $oldStatus = $guest->rsvp_status;
                $newStatus = $validated['rsvp_status'];

                // Update guest RSVP status
                $guest->update([
                    'rsvp_status' => $newStatus,
                    'rsvp_responded_at' => now(),
                ]);

                // Track confirmed guests count changes
                if ($newStatus === 'attending' && $oldStatus !== 'attending') {
                    $attendingCountChange++;
                } elseif ($oldStatus === 'attending' && $newStatus !== 'attending') {
                    $attendingCountChange--;
                }

                $updatedCount++;
            }

            if ($attendingCountChange > 0) {
                $event->increment('confirmed_guests', $attendingCountChange);
            } elseif ($attendingCountChange < 0) {
                $event->decrement('confirmed_guests', abs($attendingCountChange));
            }

            DB::commit();

            Log::info("Bulk RSVP status update for event: {$event->title}", [
                'event_id' => $event->id,
                'updated_count' => $updatedCount,
                'new_status' => $validated['rsvp_status'],
                'guest_ids' => $validated['guest_ids'],
                'user_id' => auth()->id()
            ]);

            $statusLabel = ucfirst(str_replace('_', ' ', $validated['rsvp_status']));

            return redirect()->route('events.guests.index', $event->id)
                ->with('success', "{$updatedCount} guest(s) marked as {$statusLabel} successfully");

        } catch (ValidationException $e) {
            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Invalid request data');

        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('events.index')
                ->with('error', 'Event not found');

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to bulk update RSVP status: ' . $e->getMessage(), [
                'event_id' => $eventId,
                'guest_ids' => $request->guest_ids ?? [],
                'rsvp_status' => $request->rsvp_status ?? null,
                'user_id' => auth()->id()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to update guest statuses. Please try again.');
        }
    }


    public function sendEmailInvitations(Request $request, $eventId)
    {
        try {
            $validated = $request->validate([
                'guest_ids' => 'required|array|min:1',
                'guest_ids.*' => 'required|integer|exists:event_guests,id',
                'message' => 'nullable|string|max:500',
                'priority' => ['nullable', Rule::in(['high', 'medium', 'low'])],
            ]);

            $event = Event::findOrFail($eventId);

            $guests = EventGuest::where('event_id', $eventId)
                ->whereIn('id', $validated['guest_ids'])
                ->whereNotNull('email')
                ->get();

            if ($guests->isEmpty()) {
                return redirect()->route('events.guests.index', $event->id)
                    ->with('error', 'No valid guests with email addresses found to send invitations');
            }

            $sentCount = 0;
            $failedCount = 0;
            $errors = [];
            $customMessage = $validated['message'] ?? null;
            $priority = $validated['priority'] ?? 'medium';

            foreach ($guests as $guest) {
                try {
                    $invitationToken = Str::random(64);

                    $invitationUrl = route('events.invitation.show', [
                        'event' => $event->id,
                        'token' => $invitationToken
                    ]);

                    $qrCodeData = json_encode([
                        'event_id' => $event->id,
                        'event_title' => $event->title,
                        'guest_id' => $guest->id,
                        'guest_name' => $guest->first_name . ' ' . $guest->last_name,
                        'guest_email' => $guest->email ?? null,
                        'token' => $invitationToken,
                        'invitation_url' => $invitationUrl,
                        'timestamp' => now()->timestamp
                    ]);

                    $notificationData = [
                        'title' => "Invitation to {$event->title}",
                        'message' => null,
                        'email' => $guest->email,
                        'subject' => "You're Invited: {$event->title}",
                        'priority' => $priority,
                        'event_data' => [
                            'event_id' => $event->id,
                            'guest_id' => $guest->id,
                            'event_title' => $event->title,
                            'event_date' => $event->event_date,
                            'event_venue_address' => $event->venue_address,
                            'guest_name' => $guest->full_name,
                            'guest_email' => $guest->email,
                            'invitation_url' => $invitationUrl,
                            'qr_code_data' => $qrCodeData,
                            'custom_message' => "You're Invited: {$event->title}",
                            'invitation_token' => $invitationToken,
                        ]
                    ];


                    try {
                        $notification = $this->emailService->sendNotification($notificationData);

                        DB::beginTransaction();

                        $guest->update([
                            'invitation_sent' => true,
                            'invitation_sent_at' => now(),
                            'invitation_method' => 'email',
                            'invitation_token' => $invitationToken,
                            'invitation_link' => $invitationUrl,
                        ]);

                        DB::commit();

                        Log::info('Invitation queued and guest updated', [
                            'event_id' => $event->id,
                            'guest_id' => $guest->id,
                            'email' => $guest->email,
                            'notification_id' => $notification->id,
                            'priority' => $priority,
                        ]);

                        $sentCount++;

                    } catch (ValidationException $e) {
                        DB::rollBack();
                        $failedCount++;
                        $errors[] = "Guest {$guest->full_name}: Invalid email data - " . implode(', ', $e->errors());
                        Log::error('Email validation failed for guest', [
                            'event_id' => $event->id,
                            'guest_id' => $guest->id,
                            'email' => $guest->email,
                            'errors' => $e->errors(),
                        ]);
                    } catch (Exception $e) {
                        DB::rollBack();
                        $failedCount++;
                        $errors[] = "Guest {$guest->full_name}: " . $e->getMessage();
                        Log::error('Failed to queue invitation for guest', [
                            'event_id' => $event->id,
                            'guest_id' => $guest->id,
                            'email' => $guest->email,
                            'error' => $e->getMessage(),
                        ]);
                    }

                } catch (Exception $e) {
                    $failedCount++;
                    $errors[] = "Guest {$guest->full_name}: " . $e->getMessage();
                    Log::error('General error processing guest invitation', [
                        'event_id' => $event->id,
                        'guest_id' => $guest->id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info("Bulk invitations processed for event: {$event->title}", [
                'event_id' => $event->id,
                'queued_count' => $sentCount,
                'failed_count' => $failedCount,
                'total_selected' => count($validated['guest_ids']),
                'user_id' => auth()->id(),
                'priority' => $priority,
            ]);

            // Return appropriate response
            if ($sentCount > 0) {
                $message = "{$sentCount} invitation(s) have been queued for delivery";

                if ($failedCount > 0) {
                    $message .= " ({$failedCount} failed)";
                    return redirect()->route('events.guests.index', $event->id)
                        ->with('warning', $message)
                        ->with('errors', $errors);
                }

                return redirect()->route('events.guests.index', $event->id)
                    ->with('success', $message);

            } else {
                return redirect()->route('events.guests.index', $event->id)
                    ->with('error', 'Failed to queue any invitations. Please check the errors below.')
                    ->with('errors', $errors);
            }

        } catch (ValidationException $e) {
            Log::error('Validation failed for bulk invitations', [
                'event_id' => $eventId,
                'errors' => $e->errors(),
                'user_id' => auth()->id(),
            ]);

            return redirect()->back()
                ->withErrors($e->errors())
                ->with('error', 'Invalid request data');

        } catch (ModelNotFoundException $e) {
            Log::error('Event not found for bulk invitations', [
                'event_id' => $eventId,
                'user_id' => auth()->id(),
            ]);

            return redirect()->route('events.index')
                ->with('error', 'Event not found');

        } catch (Exception $e) {
            Log::error('Failed to process bulk invitations', [
                'event_id' => $eventId,
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
            ]);

            return redirect()->back()
                ->with('error', 'Failed to send invitations: ' . $e->getMessage());
        }
    }



    public function showInvitation($eventId, $token)
    {
        try {
            $guest = EventGuest::where('event_id', $eventId)
                ->where('invitation_token', $token)
                ->firstOrFail();

            $event = Event::findOrFail($eventId);

            $invitationToken = Str::random(64);

            $invitationUrl = route('events.invitation.show', [
                'event' => $event->id,
                'guest' => $guest->id,
                'token' => $invitationToken
            ]);
            // Generate QR Code data
            $qrCodeData = json_encode([
                'event_id' => $event->id,
                'event_title' => $event->title,
                'guest_id' => $guest->id,
                'guest_name' => $guest->first_name . ' ' . $guest->last_name,
                'guest_email' => $guest->email,
                'token' => $invitationToken,
                'invitation_url' => $invitationUrl,
                'timestamp' => now()->timestamp
            ]);

            // Generate QR Code as SVG
            $qrCodeSvg = QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($qrCodeData);

            // Convert to base64 data URI
            $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

            return inertia('Events/Invitation/Show', [
                'event' => $event,
                'guest' => $guest,
                'qrCode' => $qrCodeBase64,
            ]);

        } catch (ModelNotFoundException $e) {
            abort(404, 'Invitation not found or has expired');
        }
    }



    public function sendBulkInvitations(Request $request, $eventId)
    {
        $guestIds = $request->input('guest_ids');
        $request->merge([
            'guest_ids' => $guestIds,
        ]);
        try {
            try {
                $event = Event::findOrFail($eventId);
            } catch (ModelNotFoundException $e) {
                Log::error("Event not found with ID: {$eventId}");
                return back()->with('error', 'The specified event could not be found.')->withInput();
            } catch (Exception $e) {
                Log::error("Error finding event: " . $e->getMessage());
                return back()->with('error', 'Failed to retrieve event information.')->withInput();
            }

            try {
                $validated = $request->validate([
                    'guest_ids' => 'required|array|min:1',
                    'guest_ids.*' => 'integer|exists:event_guests,id',
                    'invitation_method' => 'required|in:email,sms,whatsapp',
                    'custom_message' => 'nullable|string|max:1000',
                    'invitation_image' => 'nullable|image|max:2048',
                ]);
            } catch (ValidationException $e) {
                Log::warning("Validation failed for bulk invitations", [
                    'errors' => $e->errors(),
                    'event_id' => $eventId
                ]);
                throw $e; // Re-throw to let Laravel handle validation response
            } catch (Exception $e) {
                Log::error("Unexpected validation error: " . $e->getMessage());
                return back()->with('error', 'An error occurred while validating your request.')->withInput();
            }

            Log::info('Bulk invitation request validated', [
                'event_id' => $eventId,
                'method' => $validated['invitation_method'],
                'guest_count' => count($validated['guest_ids'])
            ]);

            $requestedGuestIds = $validated['guest_ids'];
            $invitationMethod = $validated['invitation_method'];
            $customMessage = $validated['custom_message'] ?? null;
            $invitationImage = $validated['invitation_image'] ?? null;

            $imagePath = null;
            if ($invitationImage) {
                try {
                    $imagePath = $invitationImage->store('invitations', 'public');
                    Log::info("Invitation image uploaded", ['path' => $imagePath]);
                } catch (Exception $e) {
                    Log::error("Failed to upload invitation image: " . $e->getMessage());
                    $imagePath = null;
                }
            }

            try {
                $guestsToInvite = EventGuest::where('event_id', $eventId)
                    ->whereIn('id', $requestedGuestIds)
                    ->where(function ($query) use ($invitationMethod) {
                        $query->where(function ($q) {
                            $q->where('invitation_sent', 0)
                                ->orWhereNull('invitation_sent');
                        })
                            ->orWhere(function ($q) use ($invitationMethod) {
                                $q->where('invitation_sent', 1)
                                    ->where('invitation_method', '!=', $invitationMethod);
                            });
                    })->get();

                $alreadyInvitedSameMethod = EventGuest::where('event_id', $eventId)
                    ->whereIn('id', $requestedGuestIds)
                    ->where('invitation_sent', 1)
                    ->where('invitation_method', $invitationMethod)
                    ->get();

            } catch (Exception $e) {
                Log::error("Database query failed for guests: " . $e->getMessage(), [
                    'event_id' => $eventId,
                    'guest_ids' => $requestedGuestIds
                ]);
                return back()->with('error', 'Failed to retrieve guest information from database.');
            }

            if ($guestsToInvite->isEmpty()) {
                $message = "All selected guests have already been invited via {$invitationMethod}.";
                Log::info($message, [
                    'event_id' => $eventId,
                    'total_requested' => count($requestedGuestIds)
                ]);

                return back()->with([
                    'warning' => $message,
                    'data' => [
                        'total_requested' => count($requestedGuestIds),
                        'already_invited_same_method' => $alreadyInvitedSameMethod->count(),
                        'invited_now' => 0,
                    ]
                ]);
            }

            $successCount = 0;
            $failureCount = 0;
            $resendCount = 0;
            $invitationResults = [];

            // Step 6: Process each guest
            foreach ($guestsToInvite as $guest) {
                try {
                    $isResend = $guest->invitation_sent == 1;

                    // Log attempt
                    Log::info("Attempting to send {$invitationMethod} to guest", [
                        'guest_id' => $guest->id,
                        'email' => $guest->email,
                        'phone' => $guest->phone,
                        'is_resend' => $isResend
                    ]);

                    try {
                        switch ($invitationMethod) {
                            case 'email':
                                if (empty($guest->email)) {
                                    throw new Exception("Guest has no email address");
                                }
                                $this->sendSingleInvitation($guest, $event, $customMessage, $imagePath, 'high');
                                break;

                            case 'sms':
                                if (empty($guest->phone)) {
                                    throw new Exception("Guest has no phone number for SMS");
                                }
                                $this->sendSmsInvitation($guest, $event, $customMessage);
                                break;

                            case 'whatsapp':
                                if (empty($guest->phone)) {
                                    throw new Exception("Guest has no phone number for WhatsApp");
                                }
                                $this->sendWhatsAppInvitation($guest, $event, $customMessage);
                                break;

                            default:
                                throw new Exception("Invalid invitation method: {$invitationMethod}");
                        }
                    } catch (Exception $e) {
                        Log::error("Failed to send {$invitationMethod} to guest {$guest->id}: " . $e->getMessage(), [
                            'guest_id' => $guest->id,
                            'method' => $invitationMethod,
                            'trace' => $e->getTraceAsString()
                        ]);
                        throw $e; 
                    }

                    try {
                        $guest->update([
                            'invitation_sent' => 1,
                            'invitation_sent_at' => now(),
                            'invitation_method' => $invitationMethod,
                        ]);

                        Log::info("Guest record updated successfully", ['guest_id' => $guest->id]);
                    } catch (Exception $e) {
                        Log::error("Failed to update guest record after sending invitation: " . $e->getMessage(), [
                            'guest_id' => $guest->id
                        ]);
                        // Continue counting as success since invitation was sent
                    }

                    if ($isResend) {
                        $resendCount++;
                    }

                    $invitationResults[] = [
                        'guest_id' => $guest->id,
                        'name' => trim($guest->first_name . ' ' . $guest->last_name),
                        'email' => $guest->email,
                        'phone' => $guest->phone,
                        'status' => 'sent',
                        'method' => $invitationMethod,
                        'is_resend' => $isResend,
                    ];

                    $successCount++;

                } catch (Exception $e) {
                    $failureCount++;

                    $errorMessage = $e->getMessage();
                    $invitationResults[] = [
                        'guest_id' => $guest->id,
                        'name' => trim($guest->first_name . ' ' . $guest->last_name),
                        'email' => $guest->email,
                        'phone' => $guest->phone,
                        'status' => 'failed',
                        'method' => $invitationMethod,
                        'error' => $errorMessage,
                    ];

                    // Log detailed error
                    Log::error("Failed to send {$invitationMethod} invitation to guest {$guest->id}", [
                        'guest_id' => $guest->id,
                        'error' => $errorMessage,
                        'trace' => $e->getTraceAsString()
                    ]);
                }
            }


            // Step 7: Prepare response message
            try {
                $message = "Successfully sent {$successCount} invitation(s) via {$invitationMethod}.";

                if ($resendCount > 0) {
                    $message .= " ({$resendCount} re-sent via new method)";
                }

                if ($alreadyInvitedSameMethod->count() > 0) {
                    $message .= " {$alreadyInvitedSameMethod->count()} guest(s) were skipped (already invited via {$invitationMethod}).";
                }

                if ($failureCount > 0) {
                    $message .= " {$failureCount} invitation(s) failed.";
                }

                // Log final summary
                Log::info("Bulk invitation process completed", [
                    'event_id' => $eventId,
                    'method' => $invitationMethod,
                    'success' => $successCount,
                    'failed' => $failureCount,
                    'resent' => $resendCount,
                    'skipped' => $alreadyInvitedSameMethod->count()
                ]);

                return back()->with([
                    'success' => $message,
                    'data' => [
                        'total_requested' => count($requestedGuestIds),
                        'already_invited_same_method' => $alreadyInvitedSameMethod->count(),
                        'invited_now' => $successCount,
                        'resent_different_method' => $resendCount,
                        'failed' => $failureCount,
                        'method' => $invitationMethod,
                        'invitation_results' => $invitationResults,
                        'skipped_guests' => $alreadyInvitedSameMethod->map(function ($guest) use ($invitationMethod) {
                            try {
                                return [
                                    'id' => $guest->id,
                                    'name' => trim($guest->first_name . ' ' . $guest->last_name),
                                    'email' => $guest->email,
                                    'phone' => $guest->phone,
                                    'invitation_sent_at' => $guest->invitation_sent_at ? $guest->invitation_sent_at->format('Y-m-d H:i:s') : null,
                                    'invitation_method' => $guest->invitation_method,
                                    'reason' => "Already invited via {$invitationMethod}",
                                ];
                            } catch (Exception $e) {
                                Log::error("Error mapping skipped guest: " . $e->getMessage());
                                return [
                                    'id' => $guest->id,
                                    'name' => 'Unknown',
                                    'reason' => "Already invited via {$invitationMethod}",
                                ];
                            }
                        })->toArray()
                    ]
                ]);

            } catch (Exception $e) {
                Log::error("Error preparing response message: " . $e->getMessage());
                return back()->with('success', "Processed {$successCount} invitations. Some data may be incomplete.");
            }

        } catch (ValidationException $e) {
            throw $e;
        } catch (Exception $e) {
            Log::critical("Unexpected error in sendBulkInvitations: " . $e->getMessage(), [
                'event_id' => $eventId,
                'trace' => $e->getTraceAsString()
            ]);

            return back()->with('error', 'An unexpected error occurred while processing invitations. Please try again or contact support.');
        }
    }


    public function invitationComposer(Request $request, $eventId)
    {
        $event = Event::findOrFail($eventId);

        $preselectedGuests = $request->input('preselected', []);

        if (!is_array($preselectedGuests)) {
            $preselectedGuests = [];
        }

        $preselectedGuests = array_filter($preselectedGuests, function ($id) {
            return is_numeric($id) && $id > 0;
        });

        $preselectedGuests = array_map('intval', $preselectedGuests);

        $guests = $event->guests()
            ->select([
                'id',
                'first_name',
                'last_name',
                'email',
                'category',
                'is_vip',
                'plus_ones',
            ])
            ->orderBy('last_name')
            ->orderBy('first_name')
            ->get()
            ->map(function ($guest) {
                return [
                    'id' => $guest->id,
                    'first_name' => $guest->first_name,
                    'last_name' => $guest->last_name,
                    'email' => $guest->email,
                    'category' => $guest->category ?? 'other',
                    'is_vip' => (bool) $guest->is_vip,
                    'plus_ones' => (int) $guest->plus_ones,
                ];
            });

        $validGuestIds = $guests->pluck('id')->toArray();
        $preselectedGuests = array_values(array_intersect($preselectedGuests, $validGuestIds));

        $qrCodeBase64 = $this->generateGuestQrCode($event->id, $preselectedGuests[0] ?? null);

        $eventData = [
            'id' => $event->id,
            'title' => $event->title,
            'event_date' => $event->event_date ? $event->event_date->format('Y-m-d') : null,
            'city' => $event->city,
            'country' => $event->country,
        ];

        return inertia('Events/Invitation/Invitationcomposer', [
            'event' => $eventData,
            'guests' => $guests,
            'preselectedGuests' => $preselectedGuests,
            'qrCodeBase64' => $qrCodeBase64->getData()->qr_code ?? null,
        ]);
    }


    public function generateGuestQrCode($eventId, $guestId)
    {
        $event = Event::findOrFail($eventId);
        $guest = EventGuest::findOrFail($guestId);

        if (!$event->guests()->where('id', $guestId)->exists()) {
            return response()->json(['error' => 'Guest not found in this event'], 404);
        }

        $invitationToken = Str::random(64);

        $invitationUrl = route('events.invitation.show', [
            'event' => $event->id,
            'guest' => $guest->id,
            'token' => $invitationToken
        ]);

        $qrCodeData = json_encode([
            'event_id' => $event->id,
            'event_title' => $event->title,
            'guest_id' => $guest->id,
            'guest_name' => $guest->first_name . ' ' . $guest->last_name,
            'guest_email' => $guest->email,
            'token' => $invitationToken,
            'invitation_url' => $invitationUrl,
            'timestamp' => now()->timestamp
        ]);

        try {
            $qrCodeSvg = QrCode::format('svg')
                ->size(300)
                ->errorCorrection('H')
                ->generate($qrCodeData);

            $qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

            return response()->json([
                'success' => true,
                'qr_code' => $qrCodeBase64,
                'token' => $invitationToken,
                'invitation_url' => $invitationUrl,
                'guest_id' => $guest->id,
                'guest_name' => $guest->first_name . ' ' . $guest->last_name
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Failed to generate QR code: ' . $e->getMessage()
            ], 500);
        }

    }

}
