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
class SendInAppNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 30;

    protected $userId;
    protected $type;
    protected $title;
    protected $message;
    protected $data;
    protected $notifiableType;
    protected $notifiableId;

    public function __construct(
        int $userId,
        string $type,
        string $title,
        string $message,
        ?array $data = null,
        ?string $notifiableType = null,
        ?int $notifiableId = null
    ) {
        $this->userId = $userId;
        $this->type = $type;
        $this->title = $title;
        $this->message = $message;
        $this->data = $data;
        $this->notifiableType = $notifiableType;
        $this->notifiableId = $notifiableId;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        try {
            \App\Models\Notification::create([
                'user_id' => $this->userId,
                'type' => $this->type,
                'title' => $this->title,
                'message' => $this->message,
                'data' => $this->data,
                'notifiable_type' => $this->notifiableType,
                'notifiable_id' => $this->notifiableId,
                'channel' => 'database',
                'is_read' => false,
            ]);

            Log::info("In-app notification created for user #{$this->userId}");
        } catch (\Exception $e) {
            Log::error("Failed to create notification: " . $e->getMessage());
            throw $e;
        }
    }
}