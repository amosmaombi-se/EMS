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


class SendPaymentReminder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        try {
            $booking = $this->booking->load(['user', 'event']);

            // Send email reminder
            Mail::to($booking->user->email)->send(
                new \App\Mail\PaymentReminderMail($booking)
            );

            // Create in-app notification
            SendInAppNotification::dispatch(
                $booking->user_id,
                'payment_reminder',
                'Payment Reminder',
                "Payment of ${$booking->due_amount} is due for your booking {$booking->booking_number}",
                ['booking_id' => $booking->id, 'amount' => $booking->due_amount],
                Booking::class,
                $booking->id
            );

            Log::info("Payment reminder sent for booking #{$booking->booking_number}");
        } catch (\Exception $e) {
            Log::error("Failed to send payment reminder: " . $e->getMessage());
            throw $e;
        }
    }
}
