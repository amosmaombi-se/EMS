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

class ProcessBookingConfirmation implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 120;

    protected $booking;

    public function __construct(Booking $booking)
    {
        $this->booking = $booking;
        $this->onQueue('processing');
    }

    public function handle(): void
    {
        try {
            $booking = $this->booking->load(['venue', 'items']);

            // Update venue availability
            if ($booking->venue_id) {
                \App\Models\VenueAvailability::where('venue_id', $booking->venue_id)
                    ->where('date', $booking->event_date->format('Y-m-d'))
                    ->update(['status' => 'booked']);
            }

            // Update vendor availability
            foreach ($booking->items as $item) {
                if ($item->itemable_type === Vendor::class) {
                    \App\Models\VendorAvailability::where('vendor_id', $item->itemable_id)
                        ->where('date', $booking->event_date->format('Y-m-d'))
                        ->update(['status' => 'booked']);
                }
            }

            // Send confirmation email
            SendBookingConfirmationEmail::dispatch($booking);

            // Notify vendors
            foreach ($booking->items as $item) {
                if ($item->itemable_type === Vendor::class) {
                    $vendor = Vendor::find($item->itemable_id);
                    if ($vendor) {
                        SendVendorBookingNotification::dispatch($vendor, $booking);
                    }
                }
            }

            // Generate invoice
            if (!$booking->invoices()->exists()) {
                $this->generateInvoice($booking);
            }

            Log::info("Booking confirmation processed for booking #{$booking->booking_number}");
        } catch (\Exception $e) {
            Log::error("Failed to process booking confirmation: " . $e->getMessage());
            throw $e;
        }
    }

    private function generateInvoice(Booking $booking): void
    {
        $invoice = Invoice::create([
            'booking_id' => $booking->id,
            'user_id' => $booking->user_id,
            'invoice_date' => now(),
            'due_date' => $booking->event_date->copy()->subDays(7),
            'tax_rate' => 10,
            'status' => 'sent',
        ]);

        foreach ($booking->items as $item) {
            \App\Models\InvoiceItem::create([
                'invoice_id' => $invoice->id,
                'description' => $item->item_name,
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
            ]);
        }

        $invoice->calculateTotals();
        GenerateInvoicePDF::dispatch($invoice);
    }
}
