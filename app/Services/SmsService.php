<?php

namespace App\Services;

use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use App\Models\Notification;
use App\Models\Message;
use App\Jobs\SendSmsJob;

class SmsService
{
    public function sendSms(array $data): array
    {
        $validator = Validator::make($data, [
            'message'  => 'required|string',
            'phone'    => 'required|string',
            'priority' => 'sometimes|in:high,medium,low'
        ]);

        if ($validator->fails()) {
            throw new ValidationException($validator);
        }

        $phoneNumber = $this->formatPhoneNumber($data['phone']);
        if (!$phoneNumber) {
            Log::error('Invalid phone number format', ['phone' => $data['phone']]);
            throw new Exception('Invalid phone number format');
        }

        $priority = $data['priority'] ?? 'medium';

        return DB::transaction(function () use ($data, $phoneNumber, $priority) {
            try {
                $notification = Notification::create([
                    'user_id' => Auth::id() ?? null,
                    'type'    => 'sms',
                    'channel' => 'sms',
                    'title'   => 'SMS Notification',
                    'message' => $data['message'],
                    'is_read' => false,
                    'data'    => [
                        'phone'    => $phoneNumber,
                        'priority' => $priority,
                        'message' => $data['message'],
                    ],
                ]);

                $message = Message::create([
                    'phone' => $phoneNumber,
                    'message' => $data['message'],
                    'type' => 'sms',
                    'reference_id' => $notification->id,
                    'status' => 'queued',
                    'queued_at' => now(),
                    'event_id' => $data['event_id']
                ]);

                SendSmsJob::dispatch($notification, $message)->onQueue($this->getQueueByPriority($priority));

                return [
                    'success'          => true,
                    'message'          => 'SMS queued for sending',
                    'notification_id'  => $notification->id,
                    'message_id'       => $message->id,
                ];
                
            } catch (Exception $e) {
                // Log the error and rethrow to trigger rollback
                Log::error('Failed to queue SMS', [
                    'phone' => $phoneNumber,
                    'data' => $data,
                    'error' => $e->getMessage(),
                ]);
                
                throw $e;
            }
        });
    }

    public function sendBulkSms(array $data, array $phoneNumbers): array
    {
        $results = [];
        $priority = $data['priority'] ?? 'medium';
        $successCount = 0;
        $failureCount = 0;

        // Process each phone number individually with its own transaction
        foreach ($phoneNumbers as $phoneNumber) {
            try {
                // Use transaction for each SMS to isolate failures
                $result = DB::transaction(function () use ($data, $phoneNumber, $priority) {
                    $smsData = array_merge($data, ['phone' => $phoneNumber]);
                    
                    $validator = Validator::make($smsData, [
                        'message'  => 'required|string',
                        'phone'    => 'required|string',
                        'priority' => 'sometimes|in:high,medium,low',
                    ]);

                    if ($validator->fails()) {
                        throw new ValidationException($validator);
                    }

                    $phoneNumber = $this->formatPhoneNumber($smsData['phone']);
                    if (!$phoneNumber) {
                        throw new Exception('Invalid phone number format');
                    }

                    // Create notification record
                    $notification = Notification::create([
                        'user_id' => null,
                        'type'    => 'sms',
                        'channel' => 'sms',
                        'title'   => 'SMS Notification',
                        'message' => $smsData['message'],
                        'is_read' => false,
                        'data'    => [
                            'phone'    => $phoneNumber,
                            'priority' => $priority,
                            'message'  => $smsData['message'],
                        ],
                    ]);

                    // Create message record
                    $message = Message::create([
                        'phone' => $phoneNumber,
                        'message' => $smsData['message'],
                        'type' => 'sms',
                        'reference_id' => $notification->id,
                        'status' => 'queued',
                        'queued_at' => now(),
                    ]);

                    // Dispatch SMS job with message reference
                    SendSmsJob::dispatch($notification, $message)->onQueue($this->getQueueByPriority($priority));

                    return [
                        'success'          => true,
                        'message'          => 'SMS queued for sending',
                        'notification_id'  => $notification->id,
                        'message_id'       => $message->id,
                    ];
                });

                $results[$phoneNumber] = $result;
                $successCount++;
                
            } catch (Exception $e) {
                Log::error('Bulk SMS failed', [
                    'phone'    => $phoneNumber,
                    'priority' => $priority,
                    'error'    => $e->getMessage(),
                ]);

                $results[$phoneNumber] = [
                    'success' => false,
                    'message' => $e->getMessage(),
                ];
                $failureCount++;
            }
        }

        // Log bulk operation summary
        Log::info('Bulk SMS operation completed', [
            'total' => count($phoneNumbers),
            'success' => $successCount,
            'failed' => $failureCount,
            'priority' => $priority,
        ]);

        return [
            'results' => $results,
            'summary' => [
                'total' => count($phoneNumbers),
                'success' => $successCount,
                'failed' => $failureCount,
            ]
        ];
    }

    public function updateMessageStatus(int $messageId, string $status, ?array $response = null, ?string $errorMessage = null): bool
    {
        try {
            $message = Message::findOrFail($messageId);
            
            $updateData = ['status' => $status];
            
            switch ($status) {
                case 'sent':
                    $updateData['sent_at'] = now();
                    if ($response) {
                        $updateData['response'] = $response;
                    }
                    break;
                    
                case 'failed':
                    $updateData['failed_at'] = now();
                    if ($errorMessage) {
                        $updateData['error_message'] = $errorMessage;
                    }
                    if ($response) {
                        $updateData['response'] = $response;
                    }
                    break;
                    
                case 'queued':
                    $updateData['queued_at'] = now();
                    break;
            }
            
            return $message->update($updateData);
            
        } catch (Exception $e) {
            Log::error('Failed to update message status', [
                'message_id' => $messageId,
                'status' => $status,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }

    private function isValidPhoneNumber(string $phoneNumber): bool
    {
        return preg_match('/^(0\d{8}|255\d{9})$/', $phoneNumber);
    }

    private function formatPhoneNumber(string $phoneNumber): ?string
    {
        if (!$this->isValidPhoneNumber($phoneNumber)) {
            return null;
        }

        return str_starts_with($phoneNumber, '0')
            ? '255' . substr($phoneNumber, 1)
            : $phoneNumber;
    }

    private function getQueueByPriority(string $priority): string
    {
        return match($priority) {
            'high'   => 'high',
            'low'    => 'low',
            default  => 'high',
        };
    }
}