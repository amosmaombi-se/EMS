<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Models\EventGuest;
use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\EventInvitationMail;

class SendGuestInvitationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $notification;
    public $tries = 3;
    public $backoff = 60;

  
    public function __construct(Notification $notification)
    {
        $this->notification = $notification;
    }

    public function handle(): void
    {

        try {
            $data = $this->notification->data;

            $custom_message = $this->notification->message;

            if (!isset($data['email']) || empty($data['email'])) {
                Log::warning('Event invitation job skipped: No email address', [
                    'notification_id' => $this->notification->id,
                ]);
                return;
            }

            $email = $data['email'];

            if (!isset($data['event_data']) || !is_array($data['event_data'])) {
                Log::error('Event invitation job failed: Missing or invalid event_data', [
                    'notification_id' => $this->notification->id,
                    'email' => $email,
                ]);
                throw new \Exception('Missing or invalid event_data structure');
            }

            $eventData = $data['event_data'];

            // Check for required fields
            $requiredFields = [
                'event_id' => 'Event ID',
                'guest_email' => 'Guest Email',
                'invitation_url' => 'Invitation URL',
            ];

            $missingFields = [];
            foreach ($requiredFields as $field => $fieldName) {
                if (!isset($eventData[$field]) || empty($eventData[$field])) {
                    $missingFields[] = $fieldName;
                }
            }

            if (!empty($missingFields)) {
                $errorMessage = 'Missing required fields: ' . implode(', ', $missingFields);
                Log::error('Event invitation job failed: ' . $errorMessage, [
                    'notification_id' => $this->notification->id,
                    'email' => $email,
                ]);
                throw new \Exception($errorMessage);
            }

            $eventId = $eventData['event_id'];
            $guestEmail = $eventData['guest_email'];
            $invitationUrl = $eventData['invitation_url'];
            $invitationToken = $eventData['invitation_token'] ?? null;
            $guestId = $eventData['guest_id'] ?? null;

            try {
                $event = Event::find($eventId);
                if (!$event) {
                    Log::error('Event not found for invitation', [
                        'event_id' => $eventId,
                        'notification_id' => $this->notification->id,
                    ]);
                    throw new \Exception("Event with ID {$eventId} not found");
                }
            } catch (\Exception $e) {
                Log::error('Failed to fetch event', [
                    'event_id' => $eventId,
                    'error' => $e->getMessage(),
                ]);
                throw new \Exception('Failed to fetch event details: ' . $e->getMessage());
            }

            // Fetch the Guest model
            $guest = null;
            if ($guestId) {
                try {
                    $guest = EventGuest::where('event_id', $eventId)
                        ->where('id', $guestId)
                        ->first();

                    if (!$guest) {
                        Log::warning('Guest not found', [
                            'guest_id' => $guestId,
                            'event_id' => $eventId,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::warning('Failed to fetch guest', [
                        'guest_id' => $guestId,
                        'event_id' => $eventId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Add debug logging
            Log::info('About to send email............', [
                'event_type' => gettype($event),
                'event_class' => get_class($event),
                'event_id' => $event->id ?? 'null',
                'custom_message' => $custom_message ?? null,
            ]);

            // Send the event invitation email
            Mail::to($guestEmail)->send(
                new EventInvitationMail(
                    $event,
                    $guest,
                    $invitationUrl,
                    $custom_message
                    // $eventData
                )
            );

            Log::info('Event invitation sent successfully', [
                'notification_message' => $custom_message ?? null,
                'email' => $guestEmail,
                'event_id' => $eventId,
                'guest_name' => $eventData['guest_name'] ?? 'Guest',
            ]);

            // Update guest record if we have guest
            if ($guest && $invitationToken) {
                try {
                    // Verify the token matches
                    if ($guest->invitation_token === $invitationToken) {
                        $guest->update([
                            'invitation_sent' => true,
                            'invitation_sent_at' => now(),
                            'invitation_method' => 'email',
                            'invitation_status' => 'sent',
                            'last_email_sent_at' => now(),
                        ]);

                        Log::info('Guest invitation status updated', [
                            'guest_id' => $guest->id,
                            'event_id' => $eventId,
                        ]);
                    } else {
                        Log::warning('Invitation token mismatch', [
                            'guest_id' => $guest->id,
                            'stored_token' => $guest->invitation_token,
                            'provided_token' => $invitationToken,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to update guest invitation status', [
                        'event_id' => $eventId,
                        'guest_id' => $guestId,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            // Mark notification as processed
            $this->notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        } catch (\Exception $e) {
            Log::error('Failed to send event invitation', [
                'notification_id' => $this->notification->id,
                'email' => $data['email'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Event invitation job failed permanently', [
            'notification_id' => $this->notification->id,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}