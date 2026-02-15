<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        try {
            $status = $request->input('status');
            $startDate = $request->input('start_date');
            $endDate = $request->input('end_date');

            $invoicesQuery = \App\Models\Invoice::with(['booking.event', 'user']);

            // Filter by current user if not admin
            if (!auth()->user()->hasRole('admin')) {
                $invoicesQuery->where('user_id', auth()->id());
            }

            if ($status) {
                $invoicesQuery->where('status', $status);
            }

            if ($startDate) {
                $invoicesQuery->where('invoice_date', '>=', $startDate);
            }

            if ($endDate) {
                $invoicesQuery->where('invoice_date', '<=', $endDate);
            }

            $invoices = $invoicesQuery->orderBy('invoice_date', 'desc')
                ->paginate(15)
                ->withQueryString();

            return inertia('Invoices/Index', [
                'invoices' => $invoices,
                'filters' => [
                    'status' => $status,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                ]
            ]);
        } catch (Exception $e) {
            Log::error('Failed to fetch invoices: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to fetch invoices. Please try again.');
        }
    }

    public function show($id)
    {
        try {
            $invoice = \App\Models\Invoice::with([
                'booking.event',
                'booking.venue',
                'booking.items',
                'user',
                'items'
            ])->findOrFail($id);

            // Authorization check
            if ($invoice->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('invoices.index')
                    ->with('error', 'Unauthorized');
            }

            return inertia('Invoices/Show', [
                'invoice' => $invoice
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('invoices.index')
                ->with('error', 'Invoice not found');
        } catch (Exception $e) {
            Log::error('Failed to fetch invoice: ' . $e->getMessage());
            return redirect()->route('invoices.index')
                ->with('error', 'Failed to fetch invoice details. Please try again.');
        }
    }

    public function download($id)
    {
        try {
            $invoice = \App\Models\Invoice::with([
                'booking.event',
                'booking.venue',
                'user',
                'items'
            ])->findOrFail($id);

            // Authorization check
            if ($invoice->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('invoices.index')
                    ->with('error', 'Unauthorized');
            }

            // Generate PDF
            \App\Jobs\GenerateInvoicePDF::dispatchSync($invoice);

            // Find the generated document
            $document = \App\Models\Document::where('documentable_type', \App\Models\Invoice::class)
                ->where('documentable_id', $invoice->id)
                ->where('type', 'invoice')
                ->latest()
                ->first();

            if ($document) {
                return response()->download(storage_path('app/' . $document->file_path));
            }

            return redirect()->back()
                ->with('error', 'Invoice PDF not found. Please try again.');
        } catch (ModelNotFoundException $e) {
            return redirect()->route('invoices.index')
                ->with('error', 'Invoice not found');
        } catch (Exception $e) {
            Log::error('Failed to download invoice: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to download invoice. Please try again.');
        }
    }

    public function sendEmail($id)
    {
        try {
            $invoice = \App\Models\Invoice::findOrFail($id);

            // Authorization check
            if (!auth()->user()->hasRole('admin') && !auth()->user()->hasRole('event-organizer')) {
                return redirect()->route('invoices.index')
                    ->with('error', 'Unauthorized');
            }

            DB::beginTransaction();

            $invoice->markAsSent();

            // Send invoice email
            \Mail::to($invoice->user->email)->send(
                new \App\Mail\InvoiceMail($invoice)
            );

            DB::commit();

            Log::info("Invoice sent: {$invoice->invoice_number}", [
                'invoice_id' => $invoice->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('invoices.show', $invoice->id)
                ->with('success', 'Invoice sent successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('invoices.index')
                ->with('error', 'Invoice not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to send invoice: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Failed to send invoice. Please try again.');
        }
    }
}


