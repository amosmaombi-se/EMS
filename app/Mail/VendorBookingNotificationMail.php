<?php 
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class VendorBookingNotificationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $vendor;
    public $booking;

    public function __construct($vendor, $booking)
    {
        $this->vendor = $vendor;
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject("New Booking - {$this->booking->booking_number}")
                    ->view('emails.vendor-booking-notification')
                    ->with([
                        'vendor' => $this->vendor,
                        'booking' => $this->booking,
                        'event' => $this->booking->event,
                    ]);
    }
}
