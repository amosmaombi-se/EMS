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

class SendPaymentReceiptEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 60;

    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        try {
            $payment = $this->payment->load(['booking.user', 'booking.event']);

            Mail::to($payment->user->email)->send(
                new PaymentReceiptMail($payment)
            );

            EmailLog::create([
                'user_id' => $payment->user_id,
                'to' => $payment->user->email,
                'subject' => "Payment Receipt - {$payment->payment_number}",
                'body' => "Payment receipt for your booking.",
                'template' => 'payment_receipt',
                'data' => ['payment_id' => $payment->id],
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info("Payment receipt email sent for payment #{$payment->payment_number}");
        } catch (\Exception $e) {
            Log::error("Failed to send payment receipt email: " . $e->getMessage());
            throw $e;
        }
    }
}
