<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\WhatsAppService;
use Illuminate\Support\Facades\Log;

class WhatsAppWebhookController extends Controller
{
    private $whatsappService;

    public function __construct(WhatsAppService $whatsappService)
    {
        $this->whatsappService = $whatsappService;
    }

 
    public function handleStatusCallback(Request $request)
    {
        Log::info('WhatsApp status callback received', $request->all());

        try {
            // Get Twilio data
            $data = [
                'MessageSid' => $request->input('MessageSid'),
                'MessageStatus' => $request->input('MessageStatus'),
                'ErrorCode' => $request->input('ErrorCode'),
                'ErrorMessage' => $request->input('ErrorMessage'),
                'To' => $request->input('To'),
                'From' => $request->input('From'),
                'ApiVersion' => $request->input('ApiVersion'),
            ];

            // Process callback
            $result = $this->whatsappService->handleStatusCallback($data);

            if ($result) {
                Log::info('WhatsApp callback processed successfully', [
                    'sid' => $data['MessageSid'],
                    'status' => $data['MessageStatus']
                ]);
            }

            return response()->json(['success' => true], 200);

        } catch (\Exception $e) {
            Log::error('WhatsApp callback error: ' . $e->getMessage(), [
                'request' => $request->all()
            ]);

            return response()->json(['success' => false, 'error' => $e->getMessage()], 500);
        }
    }

  
    public function handleIncoming(Request $request)
    {
        Log::info('Incoming WhatsApp message', $request->all());

        try {
            $from = $request->input('From'); 
            $body = $request->input('Body');
            $numMedia = $request->input('NumMedia', 0);

            $phone = str_replace('whatsapp:', '', $from);
            $phone = str_replace('+', '', $phone);

            Log::info('WhatsApp message received', [
                'from' => $phone,
                'body' => $body,
                'has_media' => $numMedia > 0
            ]);

            $this->processRsvpResponse($phone, $body);

            return response('', 200);

        } catch (\Exception $e) {
            Log::error('Incoming message error: ' . $e->getMessage());
            return response('', 500);
        }
    }

   
    private function processRsvpResponse($phone, $message)
    {
        $guest = \App\Models\EventGuest::where('phone', 'LIKE', '%' . substr($phone, -9))
            ->whereNotNull('event_id')
            ->first();

        if (!$guest) {
            Log::info('Guest not found for WhatsApp RSVP', ['phone' => $phone]);
            return;
        }

        $message = strtolower(trim($message));

        if (str_contains($message, 'yes') || str_contains($message, 'confirm') || str_contains($message, 'attend')) {
            $guest->update([
                'rsvp_status' => 'attending',
                'rsvp_responded_at' => now()
            ]);

            Log::info('RSVP confirmed via WhatsApp', [
                'guest_id' => $guest->id,
                'status' => 'attending'
            ]);
        } elseif (str_contains($message, 'no') || str_contains($message, 'decline') || str_contains($message, 'cannot')) {
            $guest->update([
                'rsvp_status' => 'not_attending',
                'rsvp_responded_at' => now()
            ]);

            Log::info('RSVP declined via WhatsApp', [
                'guest_id' => $guest->id,
                'status' => 'not_attending'
            ]);
        }
    }
}