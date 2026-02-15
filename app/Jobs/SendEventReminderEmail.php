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


class SendEventReminderEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 60;

    protected $event;
    protected $daysBeforeEvent;

    public function __construct(Event $event, int $daysBeforeEvent = 7)
    {
        $this->event = $event;
        $this->daysBeforeEvent = $daysBeforeEvent;
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        try {
            $event = $this->event->load('user');

            Mail::to($event->user->email)->send(
                new EventReminderMail($event, $this->daysBeforeEvent)
            );

            EmailLog::create([
                'user_id' => $event->user_id,
                'to' => $event->user->email,
                'subject' => "Event Reminder - {$event->title}",
                'body' => "Your event is {$this->daysBeforeEvent} days away!",
                'template' => 'event_reminder',
                'data' => ['event_id' => $event->id, 'days_before' => $this->daysBeforeEvent],
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info("Event reminder sent for event #{$event->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send event reminder: " . $e->getMessage());
            throw $e;
        }
    }
}
