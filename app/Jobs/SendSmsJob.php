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
use Exception;
use Carbon\Carbon;
use Throwable;

class SendSmsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $timeout = 180;
    public $tries = 3;
    public $backoff = [5, 15];

    private $notification;
    private $message;
    private $config;
    private $payload;

    public function __construct(Notification $notification, Message $message)
    {
        Log::debug('SendSmsJob constructed - START', [
            'notification_id' => $notification->id,
            'message_id' => $message->id,
            'memory' => memory_get_usage(true),
            'time' => now()->toDateTimeString()
        ]);

        $this->notification = $notification;
        $this->message = $message;
        $this->payload = $notification->data;

        $this->config = [
            'url' => config('services.sms.url', 'https://apis.onfonmedia.co.ke/v2_send'),
            'token' => config('services.sms.token', 'b25mb25taWNyb2ZpbmFuY2U6UDVqVDZSaTBPUzJaSndobGdNSG9HRGI4ZXlydlhwRTdOOTRxY0NrQkExWTNudFF6'),
            'sender_id' => config('services.sms.sender_id', 'ONFON M'),
            'dlr_url' => config('services.sms.dlr_url', 'https://kadi.windsorrecruitment.co.tz/api/test-bulk-callback'),
        ];

        // Determine priority
        $priority = $notification->data['priority'] ?? 'medium';

        // Set the queue name
        if ($priority === 'high') {
            $this->queue = 'high-priority';
            $this->onQueue('high-priority');
            $this->timeout = 60;
            $this->tries = 3;
        } else {
            $this->queue = 'sms';
            $this->onQueue('sms');
            $this->timeout = 180;
            $this->tries = 3;
        }

        Log::info('SendSmsJob constructed', [
            'notification_id' => $notification->id,
            'message_id' => $message->id,
            'phone' => $this->payload['phone'] ?? null,
            'has_token' => !empty($this->config['token']),
            'priority' => $priority,
            'queue' => $this->queue,
            'sender_id' => $this->config['sender_id']
        ]);

        if (empty($this->config['token'])) {
            Log::error('SMS API token not configured in constructor');
            throw new Exception("SMS API token not configured properly");
        }
    }

    public function handle(): void
    {
        Log::info('SendSmsJob started', [
            'notification_id' => $this->notification->id,
            'message_id' => $this->message->id,
            'attempt' => $this->attempts(),
            'queue' => $this->queue
        ]);

        try {
            if (!isset($this->payload['phone']) || empty($this->payload['phone'])) {
                throw new Exception("Missing phone number in payload");
            }

            if (!isset($this->payload['message']) || empty($this->payload['message'])) {
                throw new Exception("Missing message in payload");
            }

            $formattedNumber = $this->formatPhoneNumber($this->payload['phone']);

            if (!$formattedNumber) {
                throw new Exception("Invalid phone number format: {$this->payload['phone']}");
            }

            $requestPayload = [
                "to" => $formattedNumber,
                "from" => $this->config['sender_id'],
                "content" => $this->payload['message'],
                "dlr" => "yes",
                "dlr-url" => $this->config['dlr_url'],
                "dlr-level" => 3
            ];

            $this->message->update([
                'status' => Message::STATUS_PROCESSING,
                'provider' => 'onfonmedia_v2',
                'processing_started_at' => Carbon::now(),
            ]);

            Log::info('Sending SMS via OnfonMedia v2', [
                'notification_id' => $this->notification->id,
                'message_id' => $this->message->id,
                'to' => $formattedNumber,
                'from' => $this->config['sender_id'],
                'dlr_url' => $this->config['dlr_url'],
                'attempt' => $this->attempts()
            ]);

            $response = $this->sendSms($requestPayload);

            $this->handleSuccess($response);

        } catch (Exception $e) {
            $this->handleFailure($e);
            throw $e;
        }
    }

    private function sendSms(array $payload): array
    {
        try {
            $jsonPayload = json_encode($payload);

            Log::info('SMS API Request - Full Details', [
                'url' => $this->config['url'],
                'payload' => $payload,
                'json_payload' => $jsonPayload,
                'notification_id' => $this->notification->id,
                'message_id' => $this->message->id,
                'headers' => [
                    'Content-Type: application/json',
                    'Authorization: Basic [REDACTED]'
                ]
            ]);

            $curl = curl_init();
            
            curl_setopt_array($curl, array(
                CURLOPT_URL => $this->config['url'],
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => $jsonPayload,
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json',
                    'Authorization: Basic ' . $this->config['token']
                ),
            ));

            $responseBody = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            $curlError = curl_error($curl);
            $curlErrno = curl_errno($curl);
            
            curl_close($curl);

            if ($curlErrno) {
                throw new Exception("cURL Error ({$curlErrno}): {$curlError}");
            }

            $responseJson = json_decode($responseBody, true);

            $isSuccess = ($httpCode >= 200 && $httpCode < 300);

            $responseData = [
                'success' => $isSuccess,
                'status_code' => $httpCode,
                'body' => $responseJson ?? $responseBody,
            ];

            Log::info('SMS API Response', [
                'notification_id' => $this->notification->id,
                'message_id' => $this->message->id,
                'status_code' => $httpCode,
                'success' => $isSuccess,
                'body' => $responseData['body'],
                'raw_response' => $responseBody
            ]);

            if (!$isSuccess) {
                $errorMessage = "SMS API Error: HTTP {$httpCode}";
                
                if ($responseJson && isset($responseJson['message'])) {
                    $errorMessage = $responseJson['message'];
                } elseif ($responseJson && isset($responseJson['error'])) {
                    $errorMessage = is_array($responseJson['error']) ? json_encode($responseJson['error']) : $responseJson['error'];
                } else {
                    $errorMessage .= " - " . $responseBody;
                }

                throw new Exception($errorMessage);
            }

            return $responseData;

        } catch (Exception $e) {
            Log::error('SMS API request failed', [
                'notification_id' => $this->notification->id,
                'message_id' => $this->message->id,
                'error' => $e->getMessage(),
                'attempt' => $this->attempts()
            ]);

            throw $e;
        }
    }

    private function handleSuccess(array $response): void
    {
        $responseBody = $response['body'];
        $providerMessageId = null;

        if (is_array($responseBody)) {
            if (isset($responseBody['message_id'])) {
                $providerMessageId = $responseBody['message_id'];
            } elseif (isset($responseBody['messageId'])) {
                $providerMessageId = $responseBody['messageId'];
            } elseif (isset($responseBody['data']['message_id'])) {
                $providerMessageId = $responseBody['data']['message_id'];
            } elseif (isset($responseBody['data']['messageId'])) {
                $providerMessageId = $responseBody['data']['messageId'];
            }
        }

        $this->notification->update([
            'status' => 'sent',
            'response' => json_encode($response),
            'sent_at' => Carbon::now(),
            'provider' => 'onfonmedia_v2',
            'provider_message_id' => $providerMessageId,
        ]);

        $this->message->update([
            'status' => Message::STATUS_SENT,
            'sent_at' => Carbon::now(),
            'response' => json_encode($response),
            'provider_message_id' => $providerMessageId,
            'provider' => 'onfonmedia_v2',
            'error_message' => null,
        ]);

        Log::info('SMS sent successfully', [
            'notification_id' => $this->notification->id,
            'message_id' => $this->message->id,
            'phone' => $this->payload['phone'],
            'provider_message_id' => $providerMessageId,
            'status_code' => $response['status_code']
        ]);
    }

    private function handleFailure(Exception $e): void
    {
        $attempt = $this->attempts();
        $maxAttempts = $this->tries;
        $isPermanentFailure = $attempt >= $maxAttempts;

        $errorDetails = [
            'error' => $e->getMessage(),
            'attempt' => $attempt,
            'max_attempts' => $maxAttempts,
            'is_permanent' => $isPermanentFailure,
        ];

        // Update notification
        $notificationUpdate = [
            'status' => $isPermanentFailure ? 'permanently_failed' : 'failed',
            'error_message' => $e->getMessage(),
            'failed_at' => Carbon::now(),
        ];

        if (!$isPermanentFailure) {
            $notificationUpdate['retry_count'] = $attempt;
            $notificationUpdate['next_retry_at'] = Carbon::now()->addSeconds($this->backoff[$attempt - 1] ?? 30);
        }

        $this->notification->update($notificationUpdate);

        // Update message
        $messageUpdate = [
            'status' => $isPermanentFailure ? Message::STATUS_PERMANENTLY_FAILED : Message::STATUS_FAILED,
            'error_message' => $e->getMessage() . ($isPermanentFailure ? " (Permanent failure after {$attempt} attempts)" : " (Attempt {$attempt} of {$maxAttempts})"),
            'failed_at' => Carbon::now(),
            'response' => json_encode($errorDetails),
        ];

        $this->message->update($messageUpdate);

        Log::error('SMS sending failed', [
            'notification_id' => $this->notification->id,
            'message_id' => $this->message->id,
            'phone' => $this->payload['phone'] ?? null,
            'error' => $e->getMessage(),
            'attempt' => $attempt,
            'max_attempts' => $maxAttempts,
            'will_retry' => !$isPermanentFailure
        ]);
    }

    private function formatPhoneNumber(string $phoneNumber): ?string
    {
        $original = $phoneNumber;
        $phoneNumber = preg_replace('/\D/', '', $phoneNumber);

        if (strlen($phoneNumber) === 9 && in_array(substr($phoneNumber, 0, 1), ['6', '7', '1'])) {
            return '255' . $phoneNumber;
        }
        if (strlen($phoneNumber) === 10 && substr($phoneNumber, 0, 1) === '0') {
            return '255' . substr($phoneNumber, 1);
        }

        if (strlen($phoneNumber) === 12 && substr($phoneNumber, 0, 3) === '255') {
            return $phoneNumber;
        }

        Log::warning('Invalid phone number format', [
            'original' => $original,
            'cleaned' => $phoneNumber,
            'notification_id' => $this->notification->id,
            'message_id' => $this->message->id
        ]);

        return null;
    }

    public function failed(Throwable $exception): void
    {
        Log::critical('SendSmsJob failed permanently after all retries', [
            'notification_id' => $this->notification->id,
            'message_id' => $this->message->id,
            'phone' => $this->payload['phone'] ?? null,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts()
        ]);

        $this->notification->update([
            'status' => 'permanently_failed',
            'error_message' => 'Permanent failure after ' . $this->attempts() . ' attempts: ' . $exception->getMessage(),
            'failed_at' => Carbon::now(),
        ]);

        $this->message->update([
            'status' => Message::STATUS_PERMANENTLY_FAILED,
            'error_message' => 'Permanent failure after ' . $this->attempts() . ' attempts: ' . $exception->getMessage(),
            'failed_at' => Carbon::now(),
        ]);
    }

    public function tags(): array
    {
        return [
            'sms',
            'notification:' . $this->notification->id,
            'message:' . $this->message->id,
            'phone:' . ($this->payload['phone'] ?? 'unknown'),
            'priority:' . ($this->payload['priority'] ?? 'medium')
        ];
    }
}