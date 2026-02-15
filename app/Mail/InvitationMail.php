<?php 
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $guest;

    public function __construct($guest)
    {
        $this->guest = $guest;
    }

    public function build()
    {
        return $this->subject("You're Invited - {$this->guest->event->title}")
                    ->view('emails.invitation')
                    ->with([
                        'guest' => $this->guest,
                        'event' => $this->guest->event,
                    ]);
    }
}
