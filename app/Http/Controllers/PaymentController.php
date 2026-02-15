<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;



class PaymentController extends Controller
{
    public function create($bookingId)
    {
        try {
            $booking = Booking::with(['event', 'venue', 'items'])->findOrFail($bookingId);

            // Authorization check
            if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('bookings.index')
                    ->with('error', 'Unauthorized');
            }

            return inertia('Payments/Create', [
                'booking' => $booking,
                'paymentMethods' => ['card', 'bank_transfer', 'paypal', 'stripe', 'cash']
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('bookings.index')
                ->with('error', 'Booking not found');
        } catch (Exception $e) {
            Log::error('Failed to load payment form: ' . $e->getMessage());
            return redirect()->route('bookings.index')
                ->with('error', 'Failed to load payment form. Please try again.');
        }
    }

    public function store(Request $request, $bookingId)
    {
        try {
            $booking = Booking::findOrFail($bookingId);

            // Authorization check
            if ($booking->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('bookings.index')
                    ->with('error', 'Unauthorized');
            }

            $validated = $request->validate([
                'amount' => 'required|numeric|min:0.01|max:' . $booking->due_amount,
                'payment_method' => ['required', Rule::in(['card', 'bank_transfer', 'paypal', 'stripe', 'razorpay', 'cash'])],
                'transaction_id' => 'nullable|string|max:255',
                'notes' => 'nullable|string|max:500',
            ]);

            DB::beginTransaction();

            $payment = \App\Models\Payment::create([
                'booking_id' => $booking->id,
                'user_id' => auth()->id(),
                'amount' => $validated['amount'],
                'currency' => 'USD',
                'payment_method' => $validated['payment_method'],
                'status' => 'pending',
                'transaction_id' => $validated['transaction_id'],
                'notes' => $validated['notes'],
            ]);

            // Process payment confirmation
            \App\Jobs\ProcessPaymentConfirmation::dispatch($payment);

            DB::commit();

            Log::info("Payment created: {$payment->payment_number}", [
                'payment_id' => $payment->id,
                'booking_id' => $booking->id,
                'user_id' => auth()->id()
            ]);

            return redirect()->route('bookings.show', $booking->id)
                ->with('success', 'Payment processed successfully');
        } catch (ModelNotFoundException $e) {
            DB::rollBack();
            return redirect()->route('bookings.index')
                ->with('error', 'Booking not found');
        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Failed to process payment: ' . $e->getMessage(), [
                'request_data' => $request->all()
            ]);
            return redirect()->back()
                ->with('error', 'Failed to process payment. Please try again.')
                ->withInput();
        }
    }

    public function show($id)
    {
        try {
            $payment = \App\Models\Payment::with(['booking.event', 'user'])->findOrFail($id);

            // Authorization check
            if ($payment->user_id !== auth()->id() && !auth()->user()->hasRole('admin')) {
                return redirect()->route('bookings.index')
                    ->with('error', 'Unauthorized');
            }

            return inertia('Payments/Show', [
                'payment' => $payment
            ]);
        } catch (ModelNotFoundException $e) {
            return redirect()->route('bookings.index')
                ->with('error', 'Payment not found');
        } catch (Exception $e) {
            Log::error('Failed to fetch payment details: ' . $e->getMessage());
            return redirect()->route('bookings.index')
                ->with('error', 'Failed to fetch payment details. Please try again.');
        }
    }
}
