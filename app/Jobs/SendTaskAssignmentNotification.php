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

class SendTaskAssignmentNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 2;
    public $timeout = 30;

    protected $task;

    public function __construct(\App\Models\EventTask $task)
    {
        $this->task = $task;
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        try {
            $task = $this->task->load(['event', 'assignedUser']);

            if (!$task->assigned_to) {
                return;
            }

            SendInAppNotification::dispatch(
                $task->assigned_to,
                'task_assigned',
                'New Task Assignment',
                "You have been assigned a task: {$task->title} for event {$task->event->title}",
                [
                    'task_id' => $task->id,
                    'event_id' => $task->event_id,
                    'due_date' => $task->due_date?->format('Y-m-d'),
                ],
                \App\Models\EventTask::class,
                $task->id
            );

            Log::info("Task assignment notification sent for task #{$task->id}");
        } catch (\Exception $e) {
            Log::error("Failed to send task assignment notification: " . $e->getMessage());
            throw $e;
        }
    }
}
