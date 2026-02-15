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


class GenerateInvoicePDF implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 120;

    protected $invoice;

    public function __construct(Invoice $invoice)
    {
        $this->invoice = $invoice;
        $this->onQueue('processing');
    }

    public function handle(): void
    {
        try {
            $invoice = $this->invoice->load(['booking.user', 'items']);

            // Using DomPDF or similar
            $pdf = \PDF::loadView('invoices.pdf', compact('invoice'));
            
            $filename = "invoice-{$invoice->invoice_number}.pdf";
            $path = "invoices/{$invoice->id}/{$filename}";
            
            \Storage::put($path, $pdf->output());

            // Create document record
            \App\Models\Document::create([
                'user_id' => $invoice->user_id,
                'documentable_type' => Invoice::class,
                'documentable_id' => $invoice->id,
                'name' => "Invoice {$invoice->invoice_number}",
                'type' => 'invoice',
                'file_path' => $path,
                'file_name' => $filename,
                'mime_type' => 'application/pdf',
                'file_size' => \Storage::size($path),
            ]);

            Log::info("Invoice PDF generated for invoice #{$invoice->invoice_number}");
        } catch (\Exception $e) {
            Log::error("Failed to generate invoice PDF: " . $e->getMessage());
            throw $e;
        }
    }
}
