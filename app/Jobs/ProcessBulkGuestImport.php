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

class ProcessBulkGuestImport implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 600;

    protected $event;
    protected $filePath;
    protected $userId;

    public function __construct(Event $event, string $filePath, int $userId)
    {
        $this->event = $event;
        $this->filePath = $filePath;
        $this->userId = $userId;
        $this->onQueue('processing');
    }

    public function handle(): void
    {
        try {
            $data = \Excel::toArray([], $this->filePath)[0];
            $imported = 0;
            $errors = [];

            foreach ($data as $index => $row) {
                if ($index === 0) continue; // Skip header row

                try {
                    EventGuest::create([
                        'event_id' => $this->event->id,
                        'first_name' => $row[0] ?? '',
                        'last_name' => $row[1] ?? '',
                        'email' => $row[2] ?? null,
                        'phone' => $row[3] ?? null,
                        'category' => $row[4] ?? 'other',
                        'plus_one_allowed' => isset($row[5]) ? (bool)$row[5] : false,
                        'dietary_preference' => $row[6] ?? null,
                    ]);
                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Row {$index}: " . $e->getMessage();
                }
            }

            // Notify user
            SendInAppNotification::dispatch(
                $this->userId,
                'import_completed',
                'Guest Import Completed',
                "Successfully imported {$imported} guests. " . (count($errors) > 0 ? count($errors) . " errors occurred." : ""),
                ['event_id' => $this->event->id, 'imported' => $imported, 'errors' => $errors]
            );

            Log::info("Guest import completed for event #{$this->event->id}: {$imported} imported, " . count($errors) . " errors");
        } catch (\Exception $e) {
            Log::error("Failed to process guest import: " . $e->getMessage());
            throw $e;
        }
    }
}

