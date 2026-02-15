<?php

namespace App\Jobs;

use App\Models\Notification;
use App\Mail\GeneralNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Throwable;

class SendEmailNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    public array $backoff = [30, 60, 120];

    protected Notification $notification;

    public function __construct(Notification $notification)
    {
        // Important for queued jobs
        $this->notification = $notification;
    }

    public function handle(): void
    {
        try {
            Mail::to($this->notification->data['email'])
                ->send(new GeneralNotification(
                    $this->notification->title,
                    $this->notification->message,
                      $this->notification->data['subject'] ?? 'Notification'
                ));

            $this->notification->update([
                'status'  => 'sent',
                'sent_at' => now(),
                'error_message' => null,
            ]);

            Log::info('Email sent successfully', [
                'notification_id' => $this->notification->id,
                'email' => $this->notification->data['email'],
            ]);

        } catch (Throwable $e) {

            Log::error('Email sending failed', [
                'notification_id' => $this->notification->id,
                'email' => $this->notification->data['email'],
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $this->notification->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            // Re-throw so Laravel marks the job as failed / retries
            throw $e;
        }
    }

    /**
     * Runs after all retries are exhausted
     */
    public function failed(Throwable $exception): void
    {
        Log::critical('Email job permanently failed', [
            'notification_id' => $this->notification->id,
            'email' => $this->notification->data['email'],
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);

        $this->notification->update([
            'status' => 'failed',
            'error_message' => $exception->getMessage(),
        ]);
    }
}
