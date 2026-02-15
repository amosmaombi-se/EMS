<?php

namespace App\Services;

use App\Models\Message;
use App\Models\Notification;
use App\Jobs\SendWhatsAppMessageJob;
use Illuminate\Support\Facades\Log;
use Exception;

class WhatsAppService
{
  
    public function sendMessage(array $data)
    {
        try {
            $this->validateMessageData($data);
            
            $notification = $this->createNotification($data);
            $message = $this->createMessage($data, $notification->id);
            $this->dispatchJob($notification, $message, $data);

            return [
                'success' => true,
                'message_id' => $message->id,
                'notification_id' => $notification->id
            ];
        } catch (Exception $e) {
            Log::error('WhatsApp service error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

  
    public function sendMessageWithImage(array $data)
    {
        $data['media_type'] = 'image';
        return $this->sendMediaMessage($data);
    }

   
    public function sendMessageWithDocument(array $data)
    {
        $data['media_type'] = 'document';
        return $this->sendMediaMessage($data);
    }

  
    private function sendMediaMessage(array $data)
    {
        try {
            $this->validateMediaData($data);
            
            $notification = $this->createNotification($data);
            $message = $this->createMessage($data, $notification->id);
            $this->dispatchJob($notification, $message, $data);

            return [
                'success' => true,
                'message_id' => $message->id,
                'notification_id' => $notification->id
            ];
        } catch (Exception $e) {
            Log::error('WhatsApp media error: ' . $e->getMessage());
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

   
    private function createNotification(array $data)
    {
        return Notification::create([
            'user_id' => $data['user_id'] ?? auth()->id() ?? null,
            'event_id' => $data['event_id'] ?? null,
            'message' => $data['message'],
            'title' => 'Whatsapp notification',
            'type' => 'whatsapp',
            'data' => [
                'phone' => $data['phone'],
                'message' => $data['message'],
                'media_url' => $data['media_url'] ?? null,
                'media_type' => $data['media_type'] ?? null,
                'filename' => $data['filename'] ?? null,
                'priority' => $data['priority'] ?? 'medium'
            ],
            'status' => 'pending',
        ]);
    }

   
    private function createMessage(array $data, $notificationId)
    {
        return Message::create([
            'phone' => $this->formatPhoneNumber($data['phone']),
            'message' => $data['message'],
            'type' => 'whatsapp',
            'reference_id' => $notificationId,
            'status' => Message::STATUS_QUEUED,
            'provider' => 'twilio_whatsapp',
            'event_id' => $data['event_id'] ?? null,
            'queued_at' => now(),
        ]);
    }

   
    private function dispatchJob($notification, $message, $data)
    {
        $priority = $data['priority'] ?? 'medium';
        $queue = $priority === 'high' ? 'high-priority' : 'whatsapp';
        
        SendWhatsAppMessageJob::dispatch($notification, $message, $data)->onQueue($queue);
    }

   
    private function validateMessageData(array $data)
    {
        if (empty($data['phone'])) throw new Exception('Phone number required');
        if (empty($data['message'])) throw new Exception('Message content required');
    }

   
    private function validateMediaData(array $data)
    {
        $this->validateMessageData($data);
        if (empty($data['media_url'])) throw new Exception('Media URL required');
        if (!filter_var($data['media_url'], FILTER_VALIDATE_URL)) throw new Exception('Invalid media URL');
    }

  
    private function formatPhoneNumber(string $phone): string
    {
        $phone = preg_replace('/\D/', '', $phone);
        
        if (strlen($phone) === 9 && in_array(substr($phone, 0, 1), ['6', '7', '1'])) {
            return '255' . $phone;
        }
        if (strlen($phone) === 10 && substr($phone, 0, 1) === '0') {
            return '255' . substr($phone, 1);
        }
        return $phone;
    }

   
    public function handleStatusCallback(array $data)
    {
        try {
            $sid = $data['MessageSid'] ?? null;
            $status = $data['MessageStatus'] ?? null;

            $message = Message::where('provider_message_id', $sid)->first();
            if (!$message) return false;

            $newStatus = $this->mapTwilioStatus($status);
            $update = ['status' => $newStatus, 'response' => json_encode($data)];

            if (in_array($status, ['delivered', 'sent'])) {
                $update['sent_at'] = now();
            } elseif (in_array($status, ['failed', 'undelivered'])) {
                $update['failed_at'] = now();
                $update['error_message'] = $data['ErrorMessage'] ?? 'Delivery failed';
            }

            $message->update($update);
            
            if ($message->notification) {
                $message->notification->update(['status' => $newStatus]);
            }

            return true;
        } catch (Exception $e) {
            Log::error('WhatsApp callback error: ' . $e->getMessage());
            return false;
        }
    }

  
    private function mapTwilioStatus(?string $status): string
    {
        return match($status) {
            'queued' => Message::STATUS_QUEUED,
            'sending' => Message::STATUS_PROCESSING,
            'sent', 'delivered', 'read' => Message::STATUS_SENT,
            'failed', 'undelivered' => Message::STATUS_FAILED,
            default => Message::STATUS_PENDING
        };
    }
}