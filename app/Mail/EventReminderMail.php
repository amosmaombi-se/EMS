<?php 
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EventReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $daysBeforeEvent;

    public function __construct($event, $daysBeforeEvent)
    {
        $this->event = $event;
        $this->daysBeforeEvent = $daysBeforeEvent;
    }

    public function build()
    {
        return $this->subject("Event Reminder - {$this->event->title}")
                    ->view('emails.event-reminder')
                    ->with([
                        'event' => $this->event,
                        'days' => $this->daysBeforeEvent,
                    ]);
    }
}
