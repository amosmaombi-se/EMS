<?php
namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Models\{
    Booking,
    Payment,
    Invoice,
    Event,
    User,
    EventGuest,
    Vendor,
    Venue,
    EmailLog
};
use App\Mail\{
    BookingConfirmationMail,
    PaymentReceiptMail,
    EventReminderMail,
    InvitationMail
};


class SendDailyEventReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 600;

    public function handle(): void
    {
        try {
            $reminderDays = [7, 3, 1]; // Send reminders 7, 3, and 1 day(s) before event

            foreach ($reminderDays as $days) {
                $targetDate = now()->addDays($days)->startOfDay();

                $events = Event::whereBetween('event_date', [
                    $targetDate,
                    $targetDate->copy()->endOfDay()
                ])->whereIn('status', ['confirmed', 'planning'])->get();

                foreach ($events as $event) {
                    SendEventReminderEmail::dispatch($event, $days);
                }

                Log::info("Queued {$events->count()} event reminders for {$days} day(s) before");
            }
        } catch (\Exception $e) {
            Log::error("Failed to send daily event reminders: " . $e->getMessage());
            throw $e;
        }
    }
}

