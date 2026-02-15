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

class SendBulkInvitations implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 300;

    protected $event;

    public function __construct(Event $event)
    {
        $this->event = $event;
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        try {
            $guests = $this->event->guests()
                ->whereNotNull('email')
                ->where('invitation_sent', false)
                ->get();

            foreach ($guests as $guest) {
                SendGuestInvitationEmail::dispatch($guest)->delay(now()->addSeconds(5));
            }

            Log::info("Bulk invitations queued for event #{$this->event->id}: {$guests->count()} guests");
        } catch (\Exception $e) {
            Log::error("Failed to queue bulk invitations: " . $e->getMessage());
            throw $e;
        }
    }
}