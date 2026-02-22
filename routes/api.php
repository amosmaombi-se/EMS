<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\WhatsAppWebhookController;



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::post('/whatsapp/callback', [WhatsAppWebhookController::class, 'handleStatusCallback'])->name('whatsapp.callback');
// Incoming messages from WhatsApp (optional)
Route::post('/whatsapp/incoming', [WhatsAppWebhookController::class, 'handleIncoming'])->name('whatsapp.incoming');