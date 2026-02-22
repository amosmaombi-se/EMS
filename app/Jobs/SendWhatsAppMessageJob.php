<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Notification;
use App\Models\Message;
use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Carbon\Carbon;
use Exception;
use Throwable;

class SendWhatsAppMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 180;
    public $tries = 3;
    public $backoff = [10, 30, 60];

    private $notification;
    private $message;
    private $data;

 
    public function __construct(Notification $notification, Message $message, array $data = [])
    {
        $this->notification = $notification;
        $this->message = $message;
        $this->data = $data;

        // Set queue based on priority
        $priority = $data['priority'] ?? 'medium';
        $this->queue = $priority === 'high' ? 'high-priority' : 'whatsapp';
        $this->onQueue($this->queue);

        if ($priority === 'high') {
            $this->timeout = 60;
            $this->tries = 5;
        }
    }

   
    public function handle(): void
    {
        Log::info('WhatsApp job started', [
            'message_id' => $this->message->id,
            'attempt' => $this->attempts()
        ]);

        try {
            $this->message->update([
                'status' => Message::STATUS_PROCESSING,
                'processing_started_at' => now(),
            ]);

            $response = $this->sendViaTwilio();
            
            $this->handleSuccess($response);

        } catch (Exception $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

  
    private function sendViaTwilio()
    {
        $twilio = new Client(
            config('services.twilio.account_sid'),
            config('services.twilio.auth_token')
        );

        $from = 'whatsapp:' . config('services.twilio.whatsapp_number');
        $to = 'whatsapp:+' . $this->message->phone;

        $messageData = [
            'from' => $from,
            'body' => $this->message->message,
            'statusCallback' => route('whatsapp.callback'),
        ];

        if (!empty($this->data['media_url'])) {
            $messageData['mediaUrl'] = [$this->data['media_url']];
            
            Log::info('Sending WhatsApp with media', [
                'message_id' => $this->message->id,
                'media_type' => $this->data['media_type'] ?? 'unknown',
                'media_url' => $this->data['media_url']
            ]);
        }

        $twilioMsg = $twilio->messages->create($to, $messageData);

        Log::info('WhatsApp sent via Twilio', [
            'message_id' => $this->message->id,
            'twilio_sid' => $twilioMsg->sid,
            'status' => $twilioMsg->status
        ]);

        return [
            'success' => true,
            'twilio_sid' => $twilioMsg->sid,
            'status' => $twilioMsg->status,
            'error_code' => $twilioMsg->errorCode,
            'error_message' => $twilioMsg->errorMessage,
            'price' => $twilioMsg->price,
            'num_segments' => $twilioMsg->numSegments,
            'date_created' => $twilioMsg->dateCreated->format('Y-m-d H:i:s'),
        ];
    }

   
    private function handleSuccess(array $response): void
    {
        $status = $this->mapTwilioStatus($response['status']);

        $this->notification->update([
            'status' => 'sent',
            'response' => json_encode($response),
            'sent_at' => now(),
            'provider' => 'twilio_whatsapp',
            'provider_message_id' => $response['twilio_sid'],
        ]);

        $this->message->update([
            'status' => $status,
            'sent_at' => now(),
            'response' => json_encode($response),
            'provider_message_id' => $response['twilio_sid'],
            'error_message' => null,
        ]);

        Log::info('WhatsApp sent successfully', [
            'message_id' => $this->message->id,
            'twilio_sid' => $response['twilio_sid'],
            'status' => $status
        ]);
    }

  
    private function handleFailure(Exception $e): void
    {
        $attempt = $this->attempts();
        $isPermanent = $attempt >= $this->tries;

        $this->notification->update([
            'status' => $isPermanent ? 'permanently_failed' : 'failed',
            'error_message' => $e->getMessage(),
            'failed_at' => now(),
            'retry_count' => $attempt,
        ]);

        $this->message->update([
            'status' => $isPermanent ? Message::STATUS_PERMANENTLY_FAILED : Message::STATUS_FAILED,
            'error_message' => $e->getMessage() . ($isPermanent ? " (Permanent after {$attempt} attempts)" : " (Attempt {$attempt})"),
            'failed_at' => now(),
        ]);

        Log::error('WhatsApp failed', [
            'message_id' => $this->message->id,
            'error' => $e->getMessage(),
            'attempt' => $attempt,
            'permanent' => $isPermanent
        ]);
    }

 
    public function failed(Throwable $exception): void
    {
        Log::critical('WhatsApp permanently failed', [
            'message_id' => $this->message->id,
            'error' => $exception->getMessage()
        ]);

        $this->notification->update([
            'status' => 'permanently_failed',
            'error_message' => 'Permanent failure: ' . $exception->getMessage(),
            'failed_at' => now(),
        ]);

        $this->message->update([
            'status' => Message::STATUS_PERMANENTLY_FAILED,
            'error_message' => 'Permanent failure: ' . $exception->getMessage(),
            'failed_at' => now(),
        ]);
    }

   
    private function mapTwilioStatus(string $status): string
    {
        return match($status) {
            'queued' => Message::STATUS_QUEUED,
            'sending' => Message::STATUS_PROCESSING,
            'sent', 'delivered', 'read' => Message::STATUS_SENT,
            'failed', 'undelivered' => Message::STATUS_FAILED,
            default => Message::STATUS_PENDING
        };
    }

   
    public function tags(): array
    {
        return [
            'whatsapp',
            'message:' . $this->message->id,
            'phone:' . $this->message->phone,
        ];
    }
}