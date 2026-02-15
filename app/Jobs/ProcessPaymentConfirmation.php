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

class ProcessPaymentConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;
    public $timeout = 120;

    protected $payment;

    public function __construct(Payment $payment)
    {
        $this->payment = $payment;
        $this->onQueue('processing');
    }

    public function handle(): void
    {
        try {
            $payment = $this->payment->load('booking');

            // Mark payment as completed
            $payment->markAsCompleted();

            // Send receipt
            SendPaymentReceiptEmail::dispatch($payment);

            // Create notification
            SendInAppNotification::dispatch(
                $payment->user_id,
                'payment_received',
                'Payment Received',
                "Your payment of ${$payment->amount} has been received",
                ['payment_id' => $payment->id, 'booking_id' => $payment->booking_id],
                Payment::class,
                $payment->id
            );

            // Update invoice if fully paid
            if ($payment->booking->is_fully_paid) {
                $payment->booking->invoices->each(function ($invoice) {
                    $invoice->markAsPaid();
                });
            }

            Log::info("Payment confirmation processed for payment #{$payment->payment_number}");
        } catch (\Exception $e) {
            Log::error("Failed to process payment confirmation: " . $e->getMessage());
            throw $e;
        }
    }
}