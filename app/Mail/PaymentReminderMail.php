<?php 
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class PaymentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $booking;

    public function __construct($booking)
    {
        $this->booking = $booking;
    }

    public function build()
    {
        return $this->subject("Payment Reminder - {$this->booking->booking_number}")
                    ->view('emails.payment-reminder')
                    ->with([
                        'booking' => $this->booking,
                        'dueAmount' => $this->booking->due_amount,
                        'dueDate' => $this->booking->event_date->copy()->subDays(7),
                    ]);
    }
}
