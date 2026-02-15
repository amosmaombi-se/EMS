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

class GenerateEventReport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 300;

    protected $event;
    protected $userId;

    public function __construct(Event $event, int $userId)
    {
        $this->event = $event;
        $this->userId = $userId;
        $this->onQueue('processing');
    }

    public function handle(): void
    {
        try {
            $event = $this->event->load([
                'bookings.items',
                'bookings.payments',
                'guests',
                'tasks',
                'schedules'
            ]);

            $reportData = [
                'event' => $event,
                'total_guests' => $event->guests->count(),
                'confirmed_guests' => $event->guests->where('rsvp_status', 'attending')->count(),
                'total_bookings' => $event->bookings->count(),
                'total_revenue' => $event->bookings->sum('total_amount'),
                'total_paid' => $event->bookings->sum('paid_amount'),
                'tasks_completed' => $event->tasks->where('status', 'completed')->count(),
                'tasks_pending' => $event->tasks->where('status', 'pending')->count(),
            ];

            $pdf = \PDF::loadView('reports.event', $reportData);
            
            $filename = "event-report-{$event->id}-" . now()->format('Y-m-d') . ".pdf";
            $path = "reports/events/{$event->id}/{$filename}";
            
            \Storage::put($path, $pdf->output());

            // Notify user
            SendInAppNotification::dispatch(
                $this->userId,
                'report_generated',
                'Report Generated',
                "Your event report for {$event->title} is ready to download",
                ['path' => $path, 'event_id' => $event->id]
            );

            Log::info("Event report generated for event #{$event->id}");
        } catch (\Exception $e) {
            Log::error("Failed to generate event report: " . $e->getMessage());
            throw $e;
        }
    }
}
