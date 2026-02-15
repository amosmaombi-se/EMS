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


class SendBookingConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;
    public $backoff = [60, 120, 300]; // Retry after 1min, 2min, 5min

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        try {
            $booking = $this->booking->load(['user', 'event', 'venue', 'items']);

            Mail::to($booking->user->email)->send(
                new BookingConfirmationMail($booking)
            );

            // Log email
            EmailLog::create([
                'user_id' => $booking->user_id,
                'to' => $booking->user->email,
                'subject' => "Booking Confirmation - {$booking->booking_number}",
                'body' => "Your booking has been confirmed.",
                'template' => 'booking_confirmation',
                'data' => ['booking_id' => $booking->id],
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info("Booking confirmation email sent for booking #{$booking->booking_number}");
        } catch (\Exception $e) {
            Log::error("Failed to send booking confirmation email: " . $e->getMessage());

            EmailLog::create([
                'user_id' => $this->booking->user_id,
                'to' => $this->booking->user->email,
                'subject' => "Booking Confirmation - {$this->booking->booking_number}",
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    public function failed(\Throwable $exception): void
    {
        Log::error("Booking confirmation email job failed permanently: " . $exception->getMessage());
    }
}