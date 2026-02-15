<?php 
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
class PaymentReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public $payment;

    public function __construct($payment)
    {
        $this->payment = $payment;
    }

    public function build()
    {
        return $this->subject("Payment Receipt - {$this->payment->payment_number}")
                    ->view('emails.payment-receipt')
                    ->with([
                        'payment' => $this->payment,
                        'booking' => $this->payment->booking,
                    ]);
    }
}
