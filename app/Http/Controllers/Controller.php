<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Log;
use App\Services\SmsService;
use App\Services\EmailService;

class Controller extends BaseController
{
    public function __construct(
        private readonly SmsService $smsService,
        private readonly EmailService $emailService
    ) {
    }

    use AuthorizesRequests, ValidatesRequests;

    public function formatTanzanianNumber($mobile)
    {
        $mobile = preg_replace('/\D/', '', $mobile);

        if (strpos($mobile, '0') === 0) {
            $mobile = '255' . substr($mobile, 1);
        } elseif (strpos($mobile, '7') === 0 || strpos($mobile, '6') === 0) {
            $mobile = '255' . $mobile;
        } elseif (strpos($mobile, '+255') === 0) {
            $mobile = substr($mobile, 1); // Remove the '+'
        }

        return $mobile;
    }



    public function sendSmsInvitation($guest, $event, $customMessage = null)
    {
        try {
            $guestName = trim($guest->first_name . ' ' . $guest->last_name);
            $eventDate = \Carbon\Carbon::parse($event->date)->format('l, F j, Y');
            $eventTime = $event->time ? \Carbon\Carbon::parse($event->time)->format('g:i A') : null;

            if ($customMessage) {
                $message = str_replace('[Guest Name]', $guestName, $customMessage);
                $message = str_replace('[Event Name]', $event->name, $message);
                $message = str_replace('[Event Date]', $eventDate, $message);
                $message = str_replace('[Event Location]', $event->location, $message);
                if ($eventTime) {
                    $message = str_replace('[Event Time]', $eventTime, $message);
                }
            } else {
                $message = "Dear {$guestName},\n\n";
                $message .= "You are cordially invited to join us for {$event->name}.\n\n";
                $message .= "Event Details:\n";
                $message .= "Date: {$eventDate}\n";
                if ($eventTime) {
                    $message .= "Time: {$eventTime}\n";
                }
                $message .= "Location: {$event->location}\n\n";
                $message .= "We look forward to celebrating with you!\n\n";
                $message .= "Warm regards,\n";
                $message .= "{$event->name} Team";
            }

            $notificationData = [
                'phone' => $guest->phone,
                'title' => 'Event Invitation',
                'message' => $message,
                'subject' => "Invitation to {$event->name}",
                'priority' => 'medium',
                'event_id' => $guest->event_id
            ];

            Log::info("Sending SMS invitation", [
                'guest_id' => $guest->id,
                'guest_name' => $guestName,
                'phone' => $guest->phone,
                'event_id' => $guest->event_id,
                'event' => $event->name,
                'message_length' => strlen($message)
            ]);

            // Send via SMS service
            $status = app(SmsService::class)->sendSms($notificationData);


            if (!$status || (is_array($status) && isset($status['sms']) && !$status['sms'])) {
                throw new \Exception("SMS service returned failure status");
            }

            Log::info("SMS invitation sent successfully", [
                'guest_id' => $guest->id,
                'phone' => $guest->phone,
                'status' => $status
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error("Failed to send SMS invitation", [
                'guest_id' => $guest->id,
                'phone' => $guest->phone,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            throw $e;
        }
    }


    private function sendWhatsAppInvitation($guest, $event, $customMessage = null)
    {
        try {
            $whatsappService = app(\App\Services\WhatsAppService::class);

            $guestName = trim($guest->first_name . ' ' . $guest->last_name);
            $eventDate = \Carbon\Carbon::parse($event->event_date)->format('l, F j, Y');
            $eventTime = $event->start_time ? \Carbon\Carbon::parse($event->start_time)->format('g:i A') : null;

            // Build message
            if ($customMessage) {
                $message = str_replace([
                    '[Guest Name]',
                    '[Event Name]',
                    '[Event Date]',
                    '[Event Location]',
                    '[Event Time]'
                ], [
                    $guestName,
                    $event->name,
                    $eventDate,
                    $event->location,
                    $eventTime ?? 'TBA'
                ], $customMessage);
            } else {
                $message = "🎉 *Event Invitation* 🎉\n\n";
                $message .= "Dear *{$guestName}*,\n\n";
                $message .= "You are cordially invited to:\n";
                $message .= "📅 *{$event->name}*\n\n";
                $message .= "📆 *Date:* {$eventDate}\n";
                if ($eventTime) {
                    $message .= "⏰ *Time:* {$eventTime}\n";
                }
                $message .= "📍 *Location:* {$event->location}\n\n";
                $message .= "We look forward to celebrating with you!\n\n";
                $message .= "_Please confirm your attendance._";
            }

            $data = [
                'phone' => $guest->phone,
                'message' => $message,
                'user_id' => auth()->id(),
                'event_id' => $event->id,
                'priority' => 'medium'
            ];

            if ($event->cover_image) {
                $result = $whatsappService->sendMessageWithImage([
                    ...$data,
                    'media_url' => $event->cover_image
                ]);
            } else {
                $result = $whatsappService->sendMessage($data);
            }

            if (!$result['success']) {
                throw new \Exception($result['error'] ?? 'Failed to send WhatsApp');
            }

            $guest->update([
                'invitation_sent' => 1,
                'invitation_sent_at' => now(),
                'invitation_method' => 'whatsapp',
            ]);

            Log::info('WhatsApp invitation sent', [
                'guest_id' => $guest->id,
                'message_id' => $result['message_id']
            ]);

            return true;

        } catch (\Exception $e) {
            Log::error('WhatsApp invitation failed', [
                'guest_id' => $guest->id,
                'error' => $e->getMessage()
            ]);
            throw $e;
        }
    }

   
    private function sendWhatsAppInvitationWithDocument($guest, $event, $documentUrl, $customMessage = null)
    {
        try {
            $whatsappService = app(\App\Services\WhatsAppService::class);
            $guestName = trim($guest->first_name . ' ' . $guest->last_name);

            $message = $customMessage ?? "Dear *{$guestName}*,\n\nPlease find attached your invitation to *{$event->name}*.\n\nWe look forward to seeing you! 🎉";

            $result = $whatsappService->sendMessageWithDocument([
                'phone' => $guest->phone,
                'message' => $message,
                'media_url' => $documentUrl,
                'filename' => 'invitation.pdf',
                'user_id' => auth()->id(),
                'event_id' => $event->id,
                'priority' => 'high'
            ]);

            if ($result['success']) {
                $guest->update([
                    'invitation_sent' => 1,
                    'invitation_sent_at' => now(),
                    'invitation_method' => 'whatsapp',
                ]);
            }

            return $result['success'];

        } catch (\Exception $e) {
            Log::error('WhatsApp document invitation failed: ' . $e->getMessage());
            throw $e;
        }
    }


    public function sendBulkWhatsAppInvitations($guests, $event, $customMessage = null)
    {
        $sent = 0;
        $failed = 0;

        foreach ($guests as $guest) {
            try {
                $this->sendWhatsAppInvitation($guest, $event, $customMessage);
                $sent++;

                usleep(500000); // 0.5 second

            } catch (\Exception $e) {
                $failed++;
                Log::error('Bulk WhatsApp failed for guest', [
                    'guest_id' => $guest->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        return [
            'sent' => $sent,
            'failed' => $failed,
            'total' => count($guests)
        ];
    }


}
