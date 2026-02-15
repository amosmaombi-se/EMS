<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Notification;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Inertia\Inertia;

class ReportsController extends Controller
{
   
    public function messages(Request $request)
    {
        $query = Message::with('event'); // Load event relationship

        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        if ($request->has('provider') && $request->provider) {
            $query->where('provider', $request->provider);
        }

        if ($request->has('event_id') && $request->event_id) {
            $query->where('event_id', $request->event_id);
        }

        if ($request->has('date_from') && $request->date_from) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->has('date_to') && $request->date_to) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->has('phone') && $request->phone) {
            $query->where('phone', 'like', '%' . $request->phone . '%');
        }

        if ($request->has('search') && $request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('phone', 'like', '%' . $request->search . '%')
                    ->orWhere('message', 'like', '%' . $request->search . '%');
            });
        }

        $stats = [
            'total' => Message::count(),
            'sent' => Message::where('status', Message::STATUS_SENT)->count(),
            'failed' => Message::whereIn('status', [Message::STATUS_FAILED, Message::STATUS_PERMANENTLY_FAILED])->count(),
            'pending' => Message::where('status', Message::STATUS_PENDING)->count(),
            'processing' => Message::where('status', Message::STATUS_PROCESSING)->count(),
        ];

        $events = \App\Models\Event::select('id', 'title')->orderBy('title')->get();

        $perPage = $request->get('per_page', 10);

        $messages = $query->latest()->paginate($perPage)->withQueryString();

        return Inertia::render('Reports/Messages', [
            'messages' => $messages,
            'stats' => $stats,
            'events' => $events,
            'filters' => $request->only(['search', 'status', 'provider', 'event_id', 'date_from', 'date_to', 'phone'])
        ]);
    }

    
    public function showMessage($id)
    {
        $message = Message::findOrFail($id);

        return response()->json([
            'id' => $message->id,
            'phone' => $message->phone,
            'message' => $message->message,
            'status' => $message->status,
            'provider' => $message->provider,
            'provider_message_id' => $message->provider_message_id,
            'error_message' => $message->error_message,
            'response' => $message->response,
            'created_at' => $message->created_at->format('Y-m-d H:i:s'),
            'sent_at' => $message->sent_at ? $message->sent_at->format('Y-m-d H:i:s') : null,
            'failed_at' => $message->failed_at ? $message->failed_at->format('Y-m-d H:i:s') : null,
        ]);
    }

  
    public function retryMessage($id)
    {
        $message = Message::findOrFail($id);

        if (!in_array($message->status, [Message::STATUS_FAILED, Message::STATUS_PERMANENTLY_FAILED])) {
            return back()->with('error', 'Only failed messages can be retried');
        }

        $notification = Notification::where('id', $message->notification_id)->first();

        if (!$notification) {
            return back()->with('error', 'Notification not found');
        }

        $message->update([
            'status' => Message::STATUS_PENDING,
            'error_message' => null,
            'failed_at' => null,
        ]);

        $notification->update([
            'status' => 'pending',
            'error_message' => null,
            'failed_at' => null,
            'retry_count' => 0,
        ]);

        \App\Jobs\SendSmsJob::dispatch($notification, $message);

        return back()->with('success', 'Message queued for retry');
    }

   
    public function bulkResend(Request $request)
    {
        $request->validate([
            'message_ids' => 'required|array',
            'message_ids.*' => 'exists:messages,id',
            'reminder_message' => 'required|string|max:1000'
        ]);

        $messages = Message::whereIn('id', $request->message_ids)->get();
        $successCount = 0;
        $failedCount = 0;
        $reminderText = $request->reminder_message;

        foreach ($messages as $message) {
            try {
                $notification = Notification::where('id', $message->reference_id)->first();

                if (!$notification) {
                    $failedCount++;
                    continue;
                }

                // Create new notification for reminder
                $newNotification = Notification::create([
                    'user_id' => $notification->user_id,
                    'event_id' => $notification->event_id,
                    'title' => 'Reminer message',
                    'message' => $reminderText,
                    'type' => 'sms_reminder',
                    'channel' => 'sms',
                    'data' => array_merge($notification->data, [
                        'phone' => $message->phone,
                        'message' => $reminderText,
                        'priority' => 'medium'
                    ]),
                    'status' => 'pending',
                ]);

                // Create new message
                $newMessage = Message::create([
                    'phone' => $message->phone,
                    'message' => $reminderText,
                    'type' => 'sms',
                    'status' => Message::STATUS_PENDING,
                    'queued_at' => now(),
                    'reference_id' => $notification->id,
                    'event_id' => $message->event_id,
                ]);

                \App\Jobs\SendSmsJob::dispatch($newNotification, $newMessage);

                $successCount++;
            } catch (\Exception $e) {
                \Log::error('Bulk resend failed for message ' . $message->id . ': ' . $e->getMessage());
                $failedCount++;
            }
        }

        return back()->with('success', "{$successCount} reminders queued successfully" . ($failedCount > 0 ? ", {$failedCount} failed" : ""));
    }


    public function export(Request $request)
    {
        $format = $request->input('format', 'csv');


        $query = Message::with('event');

        if ($request->has('message_ids') && $request->message_ids) {
            $messageIds = explode(',', $request->message_ids);
            $query->whereIn('id', $messageIds);
        } else {
            if ($request->has('status') && $request->status) {
                $query->where('status', $request->status);
            }

            if ($request->has('provider') && $request->provider) {
                $query->where('provider', $request->provider);
            }

            if ($request->has('event_id') && $request->event_id) {
                $query->where('event_id', $request->event_id);
            }

            if ($request->has('date_from') && $request->date_from) {
                $query->whereDate('created_at', '>=', $request->date_from);
            }

            if ($request->has('date_to') && $request->date_to) {
                $query->whereDate('created_at', '<=', $request->date_to);
            }

            if ($request->has('search') && $request->search) {
                $query->where(function ($q) use ($request) {
                    $q->where('phone', 'like', '%' . $request->search . '%')
                        ->orWhere('message', 'like', '%' . $request->search . '%');
                });
            }
        }

        $messages = $query->get();

        if ($format === 'csv') {
            return $this->exportCsv($messages);
        }

        return back()->with('error', 'Invalid export format');
    }

    private function exportCsv($messages)
    {
        $fileName = 'messages_report_' . Carbon::now()->format('Y-m-d_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$fileName\"",
        ];

        $callback = function () use ($messages) {
            $file = fopen('php://output', 'w');

            // Header row
            fputcsv($file, [
                'ID',
                'Phone',
                'Message',
                'Status',
                'Provider',
                'Event',
                'Provider Message ID',
                'Created At',
                'Sent At',
                'Failed At',
                'Error Message'
            ]);

            // Data rows
            foreach ($messages as $message) {
                fputcsv($file, [
                    $message->id,
                    $message->phone,
                    $message->message,
                    $message->status,
                    $message->provider,
                    $message->event ? $message->event->title : 'N/A',
                    $message->provider_message_id,
                    $message->created_at->format('Y-m-d H:i:s'),
                    $message->sent_at ? $message->sent_at->format('Y-m-d H:i:s') : '',
                    $message->failed_at ? $message->failed_at->format('Y-m-d H:i:s') : '',
                    $message->error_message,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}