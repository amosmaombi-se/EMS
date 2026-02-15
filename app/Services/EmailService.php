<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use App\Jobs\SendEmailNotification;
use App\Jobs\SendGuestInvitationEmail;
use Illuminate\Validation\ValidationException;
use App\Models\Notification;

class EmailService
{
    public function sendNotification(array $data)
    {
        try {
            $validator = Validator::make($data, [
                'title' => 'required|string|max:255',
                'message' => 'nullable|string',
                'email' => 'required|email',
                'subject' => 'required|string|max:255',
                'priority' => 'sometimes|in:high,medium,low',
                'event_data' => 'nullable|array',
            ]);


            if ($validator->fails()) {
                $errors = $validator->errors()->all();
                Log::error('Notification validation failed', [
                    'email' => $data['email'] ?? null,
                    'subject' => $data['subject'] ?? null,
                    'errors' => $errors,
                ]);
                throw new ValidationException($validator);
            }

            $priority = $data['priority'] ?? 'medium';

            // Create notification record
            $notification = Notification::create([
                'user_id' => auth()->id(),
                'type' => 'email',
                'channel' => 'mail',
                'title' => $data['title'],
                'message' => $data['message'] ?? ($data['event_data']['custom_message'] ?? null),
                'is_read' => false,
                'data' => [
                    'email' => $data['email'],
                    'phone' => $data['phone'] ?? null,
                    'subject' => $data['subject'],
                    'priority' => $priority,
                    'event_data' => $data['event_data'] ?? null,
                    'custom_message' => $data['message'] ?? ($data['event_data']['custom_message'] ?? null),
                ],
            ]);

            // Dispatch appropriate job
            if (!empty($data['event_data'] ?? null)) {
                SendGuestInvitationEmail::dispatch($notification)->onQueue($this->getQueueByPriority($priority));
            } else {
                SendEmailNotification::dispatch($notification)->onQueue($this->getQueueByPriority($priority));
            }


            Log::info('Notification queued successfully', [
                'notification_id' => $notification->id,
                'email' => $data['email'],
                'subject' => $data['subject'],
                'priority' => $priority,
            ]);

            return $notification;

        } catch (ValidationException $e) {
            Log::error('Email notification validation failed', [
                'email' => $data['email'] ?? null,
                'errors' => $e->errors(),
            ]);
            throw $e;

        } catch (\Exception $e) {
            Log::error('Email notification failed to queue', [
                'email' => $data['email'] ?? null,
                'subject' => $data['subject'] ?? null,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw new \Exception('Failed to queue email notification: ' . $e->getMessage(), 0, $e);
        }
    }

    private function getQueueByPriority(string $priority): string
    {
        return match ($priority) {
            'high' => 'notifications_high',
            'low' => 'notifications_low',
            default => 'notifications_medium',
        };
    }
}