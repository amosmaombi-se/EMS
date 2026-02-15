<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class GeneralNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $title;
    public $content;
    public $emailSubject;

  
    public function __construct(string $title, string $content, string $emailSubject)
    {
        $this->title = $title;
        $this->content = $content;
        $this->emailSubject = $emailSubject ?? 'Notification';
    }

   
    public function build()
    {
        return $this->subject($this->emailSubject)
            ->markdown('emails.notification', [
                'title' => $this->title,
                'content' => $this->content
            ]);
    }
}