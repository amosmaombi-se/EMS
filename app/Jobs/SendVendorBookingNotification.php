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


class SendVendorBookingNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    protected $vendor;
    protected $booking;

    public function __construct(Vendor $vendor, Booking $booking)
    {
        $this->vendor = $vendor;
        $this->booking = $booking;
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        try {
            $vendor = $this->vendor->load('user');
            $booking = $this->booking->load('event', 'user');

            Mail::to($vendor->email)->send(
                new \App\Mail\VendorBookingNotificationMail($vendor, $booking)
            );

            Log::info("Vendor booking notification sent to: {$vendor->business_name}");
        } catch (\Exception $e) {
            Log::error("Failed to send vendor notification: " . $e->getMessage());
            throw $e;
        }
    }
}
