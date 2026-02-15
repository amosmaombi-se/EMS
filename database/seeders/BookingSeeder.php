<?php

namespace Database\Seeders;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use App\Models\{
    Booking,
    BookingItem,
    Vendor,
    Venue,
    VendorService,
    Invoice,
    InvoiceItem,
    Payment,
    Event
};

class BookingSeeder extends Seeder
{
    public function run(): void
    {
        $events = Event::where('status', '!=', 'draft')->get();
        $vendors = Vendor::where('is_active', true)->get();
        $venues = Venue::where('is_active', true)->get();

        foreach ($events as $event) {
            $venue = $venues->random();
            $selectedVendors = $vendors->random(rand(2, 4));

            $booking = Booking::create([
                'event_id' => $event->id,
                'user_id' => $event->user_id,
                'venue_id' => $venue->id,
                'booking_date' => now()->subDays(rand(10, 60)),
                'event_date' => $event->event_date,
                'event_end_date' => $event->event_end_date,
                'subtotal' => 0,
                'tax_amount' => 0,
                'discount_amount' => 0,
                'total_amount' => 0,
                'paid_amount' => 0,
                'due_amount' => 0,
                'status' => ['pending', 'confirmed', 'in_progress'][rand(0, 2)],
                'payment_status' => 'unpaid',
                'customer_notes' => 'Looking forward to a great event!',
            ]);

            // Add Venue as Booking Item
            BookingItem::create([
                'booking_id' => $booking->id,
                'itemable_type' => Venue::class,
                'itemable_id' => $venue->id,
                'item_name' => $venue->name,
                'description' => 'Venue rental for the event',
                'quantity' => 1,
                'unit_price' => $venue->base_price_per_day,
            ]);

            // Add Vendor Services as Booking Items
            foreach ($selectedVendors as $vendor) {
                $service = $vendor->services->random();

                $quantity = $service->pricing_type === 'per_person' 
                    ? $event->expected_guests 
                    : 1;

                BookingItem::create([
                    'booking_id' => $booking->id,
                    'itemable_type' => VendorService::class,
                    'itemable_id' => $service->id,
                    'item_name' => $service->name . ' - ' . $vendor->business_name,
                    'description' => $service->description,
                    'quantity' => $quantity,
                    'unit_price' => $service->base_price,
                ]);
            }

            // Calculate totals
            $booking->calculateTotals();

            // Create Invoice
            $invoice = Invoice::create([
                'booking_id' => $booking->id,
                'user_id' => $booking->user_id,
                'invoice_date' => $booking->booking_date,
                'due_date' => $booking->event_date->copy()->subDays(7),
                'tax_rate' => 10,
                'status' => 'sent',
                'notes' => 'Payment due 7 days before event date',
            ]);

            // Add Invoice Items from Booking Items
            foreach ($booking->items as $bookingItem) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'description' => $bookingItem->item_name,
                    'quantity' => $bookingItem->quantity,
                    'unit_price' => $bookingItem->unit_price,
                ]);
            }

            $invoice->calculateTotals();

            // Create some payments (50% chance)
            if (rand(0, 1)) {
                $paymentAmount = $booking->total_amount * (rand(30, 70) / 100);

                $payment = Payment::create([
                    'booking_id' => $booking->id,
                    'user_id' => $booking->user_id,
                    'amount' => $paymentAmount,
                    'currency' => 'USD',
                    'payment_method' => ['card', 'bank_transfer', 'paypal'][rand(0, 2)],
                    'status' => 'completed',
                    'transaction_id' => 'TXN-' . strtoupper(Str::random(10)),
                    'payment_gateway' => 'stripe',
                    'paid_at' => now()->subDays(rand(1, 30)),
                ]);

                $payment->markAsCompleted();
            }

            // Confirm some bookings
            if ($booking->status === 'confirmed') {
                $booking->confirm();
            }
        }

        $this->command->info('Bookings seeded successfully!');
    }
}
