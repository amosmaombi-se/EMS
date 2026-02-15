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

class SendPaymentReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 1;
    public $timeout = 600;

    public function handle(): void
    {
        try {
            // Find bookings with outstanding payments due within 7 days
            $dueDate = now()->addDays(7);

            $bookings = Booking::where('payment_status', '!=', 'paid')
                ->where('status', 'confirmed')
                ->where('event_date', '<=', $dueDate)
                ->where('due_amount', '>', 0)
                ->get();

            foreach ($bookings as $booking) {
                SendPaymentReminder::dispatch($booking);
            }

            Log::info("Queued {$bookings->count()} payment reminders");
        } catch (\Exception $e) {
            Log::error("Failed to send payment reminders: " . $e->getMessage());
            throw $e;
        }
    }
}
