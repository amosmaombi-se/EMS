<?php

namespace App\Services;

use App\Models\Event;
use App\Models\EventGuest;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class InvitationMessageGenerator
{
   
    public function generateMessage(Event $event, EventGuest $guest, ?string $customMessage = null): string
    {
        // If custom message provided, return it with personalization
        if ($customMessage) {
            $personalizedMessage = $this->personalizeMessage($customMessage, $event, $guest);
            
            // Log custom message
            Log::info('Custom Invitation Message Generated', [
                'event_id' => $event->id,
                'event_title' => $event->title,
                'guest_id' => $guest->id,
                'guest_name' => $guest->first_name . ' ' . $guest->last_name,
                'message_type' => 'custom',
                'original_message' => $customMessage,
                'personalized_message' => $personalizedMessage,
                'timestamp' => now()->toDateTimeString(),
            ]);
            
            return $personalizedMessage;
        }

        // Generate message based on event type and details
        $defaultMessage = $this->generateDefaultMessage($event, $guest);
        
        // Log default message
        Log::info('Default Invitation Message Generated', [
            'event_id' => $event->id,
            'event_title' => $event->title,
            'event_type' => $event->eventType->name ?? 'unknown',
            'guest_id' => $guest->id,
            'guest_name' => $guest->first_name . ' ' . $guest->last_name,
            'message_type' => 'default',
            'message_preview' => substr($defaultMessage, 0, 100) . '...',
            'timestamp' => now()->toDateTimeString(),
        ]);
        
        return $defaultMessage;
    }

    /**
     * Generate default message based on event details
     */
    private function generateDefaultMessage(Event $event, EventGuest $guest): string
    {
        $messages = [
            'wedding' => $this->getWeddingMessage($event, $guest),
            'birthday' => $this->getBirthdayMessage($event, $guest),
            'corporate' => $this->getCorporateMessage($event, $guest),
            'conference' => $this->getConferenceMessage($event, $guest),
            'party' => $this->getPartyMessage($event, $guest),
            'fundraiser' => $this->getFundraiserMessage($event, $guest),
            'seminar' => $this->getSeminarMessage($event, $guest),
            'workshop' => $this->getWorkshopMessage($event, $guest),
            'meeting' => $this->getMeetingMessage($event, $guest),
            'gala' => $this->getGalaMessage($event, $guest),
            'anniversary' => $this->getAnniversaryMessage($event, $guest),
            'graduation' => $this->getGraduationMessage($event, $guest),
            'baby_shower' => $this->getBabyShowerMessage($event, $guest),
            'concert' => $this->getConcertMessage($event, $guest),
            'exhibition' => $this->getExhibitionMessage($event, $guest),
        ];

        $eventTypeName = $event->eventType->name ?? 'event';
        $eventTypeSlug = strtolower(str_replace(' ', '_', $eventTypeName));

        return $messages[$eventTypeSlug] ?? $this->getGenericMessage($event, $guest);
    }

    /**
     * Wedding invitation message
     */
    private function getWeddingMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "With hearts full of joy, we invite you to celebrate the union of two souls in love.\n\n"
            . "Join us as we exchange our vows and begin our journey together as one.\n\n"
            . "📅 {$date}\n"
            . "🕐 {$time}\n"
            . "📍 {$event->venue}\n\n"
            . "Your presence would make our special day even more memorable. We can't wait to celebrate with you!\n\n"
            . "With love and excitement,\n"
            . "The Happy Couple";
    }

    /**
     * Birthday invitation message
     */
    private function getBirthdayMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Hey {$guest->first_name}! 🎉\n\n"
            . "You're invited to help me celebrate another amazing year of life!\n\n"
            . "Let's make it a night to remember with good friends, great music, and lots of fun!\n\n"
            . "🎂 When: {$date} at {$time}\n"
            . "📍 Where: {$event->venue}\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Don't miss out on the celebration! Your presence is the best gift.\n\n"
            . "See you there! 🥳";
    }

    /**
     * Corporate event message
     */
    private function getCorporateMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "You are cordially invited to attend {$event->title}.\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Event Details:\n"
            . "📅 Date: {$date}\n"
            . "🕐 Time: {$time}\n"
            . "📍 Venue: {$event->venue}\n\n"
            . "We look forward to your participation in this important event.\n\n"
            . "Best regards,\n"
            . "Event Organizing Committee";
    }

    /**
     * Conference invitation message
     */
    private function getConferenceMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('F j, Y');
        $endDate = $event->end_date ? Carbon::parse($event->end_date)->format('F j, Y') : null;
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "We are delighted to invite you to {$event->title}.\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Conference Details:\n"
            . "📅 Date: {$date}" . ($endDate ? " - {$endDate}" : "") . "\n"
            . "🕐 Time: {$time}\n"
            . "📍 Location: {$event->venue}\n\n"
            . "This promises to be an enriching experience with industry leaders, networking opportunities, and valuable insights.\n\n"
            . "We hope to see you there!\n\n"
            . "Best regards,\n"
            . "Conference Organizing Team";
    }

    /**
     * Party invitation message
     */
    private function getPartyMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Hey {$guest->first_name}! 🎊\n\n"
            . "Get ready to party! You're invited to {$event->title}!\n\n"
            . "🎉 When: {$date} at {$time}\n"
            . "📍 Where: {$event->venue}\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Bring your dancing shoes and get ready for an unforgettable night!\n\n"
            . "Can't wait to see you there! 🥳🎵";
    }

    /**
     * Fundraiser invitation message
     */
    private function getFundraiserMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "We invite you to join us for a meaningful evening at {$event->title}.\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Your presence and support can make a real difference.\n\n"
            . "Event Details:\n"
            . "📅 {$date}\n"
            . "🕐 {$time}\n"
            . "📍 {$event->venue}\n\n"
            . "Together, we can create positive change and make an impact.\n\n"
            . "Thank you for your generosity and support.\n\n"
            . "Warm regards,\n"
            . "Organizing Committee";
    }

    /**
     * Seminar/Workshop invitation message
     */
    private function getSeminarMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "You are invited to participate in {$event->title}.\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Session Details:\n"
            . "📅 Date: {$date}\n"
            . "🕐 Time: {$time}\n"
            . "📍 Venue: {$event->venue}\n\n"
            . "This session promises to be an excellent learning opportunity with practical insights and valuable takeaways.\n\n"
            . "We look forward to your participation.\n\n"
            . "Best regards,\n"
            . "Event Coordinator";
    }

    /**
     * Workshop invitation message
     */
    private function getWorkshopMessage(Event $event, EventGuest $guest): string
    {
        return $this->getSeminarMessage($event, $guest);
    }

    /**
     * Meeting invitation message
     */
    private function getMeetingMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "You are invited to attend: {$event->title}\n\n"
            . ($event->description ? "Agenda:\n{$event->description}\n\n" : "")
            . "Meeting Details:\n"
            . "📅 Date: {$date}\n"
            . "🕐 Time: {$time}\n"
            . "📍 Location: {$event->venue}\n\n"
            . "Your attendance is important. Please confirm your participation.\n\n"
            . "Best regards,\n"
            . "Meeting Organizer";
    }

    /**
     * Gala invitation message
     */
    private function getGalaMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "You are cordially invited to an elegant evening at {$event->title}.\n\n"
            . "Join us for a sophisticated celebration featuring fine dining, entertainment, and distinguished company.\n\n"
            . "Event Details:\n"
            . "📅 {$date}\n"
            . "🕐 {$time}\n"
            . "📍 {$event->venue}\n"
            . "👔 Dress Code: Formal/Black Tie\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "We would be honored by your presence at this exclusive event.\n\n"
            . "With warm regards,\n"
            . "Gala Committee";
    }

    /**
     * Anniversary celebration message
     */
    private function getAnniversaryMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "Join us as we celebrate {$event->title}!\n\n"
            . "Your presence would add joy to our special milestone celebration.\n\n"
            . "Celebration Details:\n"
            . "📅 {$date}\n"
            . "🕐 {$time}\n"
            . "📍 {$event->venue}\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Let's make beautiful memories together as we mark this wonderful occasion.\n\n"
            . "With love and gratitude,\n"
            . "The Celebrants";
    }

    /**
     * Graduation celebration message
     */
    private function getGraduationMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "You're invited to celebrate a major milestone - {$event->title}! 🎓\n\n"
            . "Join us as we honor this significant achievement and look forward to new beginnings.\n\n"
            . "Celebration Details:\n"
            . "📅 {$date}\n"
            . "🕐 {$time}\n"
            . "📍 {$event->venue}\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Your support and encouragement have been invaluable. Let's celebrate together!\n\n"
            . "Best wishes,\n"
            . "The Graduate & Family";
    }

    /**
     * Baby shower invitation message
     */
    private function getBabyShowerMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name},\n\n"
            . "You're invited to shower love and blessings at {$event->title}! 👶💕\n\n"
            . "Join us for a special celebration as we prepare to welcome a precious new arrival.\n\n"
            . "Party Details:\n"
            . "📅 {$date}\n"
            . "🕐 {$time}\n"
            . "📍 {$event->venue}\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Your presence would make this day even more special!\n\n"
            . "With love,\n"
            . "The Expecting Family";
    }

    /**
     * Concert invitation message
     */
    private function getConcertMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Hey {$guest->first_name}! 🎵\n\n"
            . "Get ready for an amazing night of music! You're invited to {$event->title}!\n\n"
            . "Concert Details:\n"
            . "📅 {$date}\n"
            . "🕐 {$time}\n"
            . "📍 {$event->venue}\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Don't miss this incredible live performance!\n\n"
            . "See you there! 🎸🎤";
    }

    /**
     * Exhibition invitation message
     */
    private function getExhibitionMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('F j, Y');
        $endDate = $event->end_date ? Carbon::parse($event->end_date)->format('F j, Y') : null;
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "You are cordially invited to the opening of {$event->title}.\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Exhibition Details:\n"
            . "📅 " . ($endDate ? "Opening: {$date}\n   Exhibition runs through: {$endDate}" : $date) . "\n"
            . "🕐 Opening Reception: {$time}\n"
            . "📍 {$event->venue}\n\n"
            . "Join us for an evening of art, culture, and inspiration.\n\n"
            . "We look forward to seeing you.\n\n"
            . "Warm regards,\n"
            . "Exhibition Curator";
    }

    /**
     * Generic event message
     */
    private function getGenericMessage(Event $event, EventGuest $guest): string
    {
        $date = Carbon::parse($event->start_date)->format('l, F j, Y');
        $time = Carbon::parse($event->start_date)->format('g:i A');
        
        return "Dear {$guest->first_name} {$guest->last_name},\n\n"
            . "You are invited to {$event->title}.\n\n"
            . ($event->description ? "{$event->description}\n\n" : "")
            . "Event Details:\n"
            . "📅 Date: {$date}\n"
            . "🕐 Time: {$time}\n"
            . "📍 Venue: {$event->venue}\n\n"
            . "We would be delighted to have you join us for this special occasion.\n\n"
            . "Looking forward to seeing you!\n\n"
            . "Best regards,\n"
            . "Event Organizer";
    }

    /**
     * Personalize custom message with event and guest details
     */
    private function personalizeMessage(string $message, Event $event, EventGuest $guest): string
    {
        $replacements = [
            '{guest_name}' => $guest->first_name . ' ' . $guest->last_name,
            '{first_name}' => $guest->first_name,
            '{last_name}' => $guest->last_name,
            '{event_title}' => $event->title,
            '{event_date}' => Carbon::parse($event->start_date)->format('l, F j, Y'),
            '{event_time}' => Carbon::parse($event->start_date)->format('g:i A'),
            '{venue}' => $event->venue,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $message);
    }

    /**
     * Generate email subject line
     */
    public function generateSubject(Event $event): string
    {
        $eventTypeName = $event->eventType->name ?? 'Event';
        
        $subjects = [
            'wedding' => "You're Invited to Our Wedding! 💍",
            'birthday' => "Join Me for a Birthday Celebration! 🎂",
            'corporate' => "Invitation: {$event->title}",
            'conference' => "You're Invited: {$event->title}",
            'party' => "Party Invitation: {$event->title} 🎉",
            'fundraiser' => "Join Us for {$event->title}",
            'seminar' => "Invitation to Attend: {$event->title}",
            'workshop' => "Workshop Invitation: {$event->title}",
            'meeting' => "Meeting Invitation: {$event->title}",
            'gala' => "Cordial Invitation: {$event->title}",
            'anniversary' => "Celebrate With Us: {$event->title}",
            'graduation' => "Graduation Celebration: {$event->title} 🎓",
            'baby_shower' => "Baby Shower Invitation: {$event->title} 👶",
            'concert' => "Concert Ticket: {$event->title} 🎵",
            'exhibition' => "Exhibition Opening: {$event->title}",
        ];

        $eventTypeSlug = strtolower(str_replace(' ', '_', $eventTypeName));
        
        return $subjects[$eventTypeSlug] ?? "You're Invited: {$event->title}";
    }
}