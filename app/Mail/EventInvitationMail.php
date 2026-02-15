<?php

namespace App\Mail;

use App\Models\Event;
use App\Models\EventGuest;
use App\Services\InvitationMessageGenerator;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Illuminate\Support\Facades\Log;

class EventInvitationMail extends Mailable
{
    use Queueable, SerializesModels;

    public Event $event;
    public ?EventGuest $guest;
    public string $invitationUrl;
    public string $qrCodeBase64;
    public string $generatedMessage;

    public function __construct(
        Event $event,
        ?EventGuest $guest,
        string $invitationUrl,
        ?string $customMessage = null
    ) {
        $this->event = $event;
        $this->guest = $guest;
        $this->invitationUrl = $invitationUrl;

        // Generate QR Code
        $qrPayload = json_encode([
            'event_id'   => $event->id,
            'guest_id'   => $guest?->id,
            'guest_name' => $guest
                ? trim($guest->first_name . ' ' . $guest->last_name)
                : null,
            'token'      => $guest?->invitation_token,
        ]);

        $qrCodeSvg = QrCode::format('svg')
            ->size(300)
            ->errorCorrection('H')
            ->generate($qrPayload);

        $this->qrCodeBase64 = 'data:image/svg+xml;base64,' . base64_encode($qrCodeSvg);

        // Generate Message (with or without custom message)
        $messageGenerator = new InvitationMessageGenerator();
        $this->generatedMessage = $messageGenerator->generateMessage(
            $event,
            $guest,
            $customMessage
        );

        // Log email creation
        Log::info('Email Invitation Created..', [
            'event_id' => $event->id,
            'event_title' => $event->title,
            'guest_id' => $guest?->id,
            'guest_name' => $guest ? $guest->first_name . ' ' . $guest->last_name : 'N/A',
            'guest_email' => $guest?->email,
            'has_custom_message' => !is_null($customMessage),
            'custom_message' => $customMessage,
            'invitation_url' => $invitationUrl,
            'qr_code_generated' => !empty($this->qrCodeBase64),
            'timestamp' => now()->toDateTimeString(),
        ]);
    }

    public function build()
    {
        $subjectGenerator = new InvitationMessageGenerator();
        $subject = $subjectGenerator->generateSubject($this->event);

        // Log email build process
        Log::info('Email Invitation Building', [
            'event_id' => $this->event->id,
            'guest_id' => $this->guest?->id,
            'subject' => $subject,
            'view' => 'emails.event-invitation',
            'message' => $this->generatedMessage,
            'timestamp' => now()->toDateTimeString(),
        ]);

        return $this->subject($subject)
            ->view('emails.event-invitation')
            ->with([
                'event'           => $this->event,
                'guest'           => $this->guest,
                'invitationUrl'   => $this->invitationUrl,
                'qrCodeBase64'    => $this->qrCodeBase64,
                'generatedMessage'=> $this->generatedMessage,
            ]);
    }
}