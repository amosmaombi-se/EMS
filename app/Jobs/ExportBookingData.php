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

class ExportBookingData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 600;

    protected $filters;
    protected $userId;

    public function __construct(array $filters, int $userId)
    {
        $this->filters = $filters;
        $this->userId = $userId;
        $this->onQueue('processing');
    }

    public function handle(): void
    {
        try {
            $query = Booking::with(['user', 'event', 'venue']);

            // Apply filters
            if (isset($this->filters['status'])) {
                $query->where('status', $this->filters['status']);
            }
            if (isset($this->filters['date_from'])) {
                $query->where('event_date', '>=', $this->filters['date_from']);
            }
            if (isset($this->filters['date_to'])) {
                $query->where('event_date', '<=', $this->filters['date_to']);
            }

            $bookings = $query->get();

            $filename = 'bookings-export-' . now()->format('Y-m-d-His') . '.csv';
            $path = "exports/{$this->userId}/{$filename}";

            \Storage::put($path, $this->generateCSV($bookings));

            // Notify user
            SendInAppNotification::dispatch(
                $this->userId,
                'export_completed',
                'Export Completed',
                "Your booking data export is ready to download",
                ['path' => $path, 'filename' => $filename]
            );

            Log::info("Booking export completed for user #{$this->userId}: {$bookings->count()} records");
        } catch (\Exception $e) {
            Log::error("Failed to export booking data: " . $e->getMessage());
            throw $e;
        }
    }

    private function generateCSV($bookings): string
    {
        $csv = "Booking Number,Customer,Event,Venue,Event Date,Status,Payment Status,Total Amount,Paid Amount,Due Amount\n";

        foreach ($bookings as $booking) {
            $csv .= implode(',', [
                $booking->booking_number,
                $booking->user->full_name,
                $booking->event->title,
                $booking->venue?->name ?? 'N/A',
                $booking->event_date->format('Y-m-d'),
                $booking->status,
                $booking->payment_status,
                $booking->total_amount,
                $booking->paid_amount,
                $booking->due_amount,
            ]) . "\n";
        }

        return $csv;
    }
}
