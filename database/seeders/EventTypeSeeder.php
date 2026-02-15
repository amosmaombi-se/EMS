<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    User,
    Role,
    Permission,
    EventType,
    EventPackage,
    VendorCategory,
    Vendor,
    VendorService,
    VendorAvailability,
    VendorPortfolio,
    Venue,
    VenueAvailability,
    Event,
    EventSchedule,
    EventChecklist,
    EventTask,
    EventGuest,
    Booking,
    BookingItem,
    Payment,
    Invoice,
    InvoiceItem,
    Review,
    Setting
};
class EventTypeSeeder extends Seeder
{
  public function run(): void
    {
        $eventTypes = [
            [
                'name' => 'Wedding',
                'slug' => 'wedding',
                'description' => 'Wedding ceremonies and receptions',
                'icon' => '💍',
                'color' => '#E91E63',
                'typical_duration_hours' => 8,
                'default_checklist' => [
                    'Book venue',
                    'Hire photographer',
                    'Choose catering',
                    'Send invitations',
                    'Arrange decorations',
                    'Book entertainment',
                ],
                'sort_order' => 1,
            ],
            [
                'name' => 'Birthday Party',
                'slug' => 'birthday-party',
                'description' => 'Birthday celebrations',
                'icon' => '🎂',
                'color' => '#FF9800',
                'typical_duration_hours' => 4,
                'default_checklist' => [
                    'Book venue',
                    'Order cake',
                    'Arrange decorations',
                    'Plan entertainment',
                    'Send invitations',
                ],
                'sort_order' => 2,
            ],
            [
                'name' => 'Corporate Event',
                'slug' => 'corporate-event',
                'description' => 'Business meetings, conferences, and seminars',
                'icon' => '💼',
                'color' => '#2196F3',
                'typical_duration_hours' => 6,
                'default_checklist' => [
                    'Book conference venue',
                    'Arrange AV equipment',
                    'Organize catering',
                    'Prepare materials',
                    'Send invitations',
                ],
                'sort_order' => 3,
            ],
            [
                'name' => 'Send-off Party',
                'slug' => 'send-off-party',
                'description' => 'Farewell and going away parties',
                'icon' => '👋',
                'color' => '#9C27B0',
                'typical_duration_hours' => 3,
                'default_checklist' => [
                    'Choose venue',
                    'Arrange food and drinks',
                    'Prepare gift',
                    'Create memory book',
                    'Invite guests',
                ],
                'sort_order' => 4,
            ],
            [
                'name' => 'Anniversary',
                'slug' => 'anniversary',
                'description' => 'Anniversary celebrations',
                'icon' => '💐',
                'color' => '#F44336',
                'typical_duration_hours' => 5,
                'default_checklist' => [
                    'Book venue',
                    'Hire photographer',
                    'Arrange decorations',
                    'Plan menu',
                    'Send invitations',
                ],
                'sort_order' => 5,
            ],
            [
                'name' => 'Baby Shower',
                'slug' => 'baby-shower',
                'description' => 'Baby shower celebrations',
                'icon' => '👶',
                'color' => '#00BCD4',
                'typical_duration_hours' => 3,
                'default_checklist' => [
                    'Choose venue',
                    'Plan games',
                    'Arrange decorations',
                    'Organize food',
                    'Prepare gift registry',
                ],
                'sort_order' => 6,
            ],
            [
                'name' => 'Graduation Party',
                'slug' => 'graduation-party',
                'description' => 'Graduation celebrations',
                'icon' => '🎓',
                'color' => '#4CAF50',
                'typical_duration_hours' => 4,
                'default_checklist' => [
                    'Book venue',
                    'Arrange catering',
                    'Prepare decorations',
                    'Create photo display',
                    'Send invitations',
                ],
                'sort_order' => 7,
            ],
            [
                'name' => 'Conference',
                'slug' => 'conference',
                'description' => 'Professional conferences and seminars',
                'icon' => '📊',
                'color' => '#607D8B',
                'typical_duration_hours' => 8,
                'default_checklist' => [
                    'Book conference center',
                    'Arrange speakers',
                    'Setup registration',
                    'Organize catering',
                    'Prepare materials',
                ],
                'sort_order' => 8,
            ],
        ];

        foreach ($eventTypes as $type) {
            $eventType = EventType::create($type);

            // Create packages for each event type
            $packages = [
                [
                    'name' => 'Basic',
                    'slug' => 'basic',
                    'description' => 'Essential services for your event',
                    'base_price' => rand(500, 1000),
                    'min_guests' => 10,
                    'max_guests' => 50,
                    'included_services' => ['Venue rental', 'Basic decorations', 'Standard catering'],
                    'features' => ['4 hours duration', 'Basic setup', 'Cleanup service'],
                    'sort_order' => 1,
                ],
                [
                    'name' => 'Standard',
                    'slug' => 'standard',
                    'description' => 'Popular choice with enhanced services',
                    'base_price' => rand(1500, 2500),
                    'min_guests' => 50,
                    'max_guests' => 150,
                    'included_services' => ['Premium venue', 'Enhanced decorations', 'Deluxe catering', 'Photography'],
                    'features' => ['6 hours duration', 'Professional setup', 'Dedicated coordinator', 'Cleanup service'],
                    'sort_order' => 2,
                ],
                [
                    'name' => 'Premium',
                    'slug' => 'premium',
                    'description' => 'All-inclusive luxury experience',
                    'base_price' => rand(3000, 5000),
                    'min_guests' => 100,
                    'max_guests' => 300,
                    'included_services' => ['Luxury venue', 'Premium decorations', 'Gourmet catering', 'Photography & Videography', 'Entertainment'],
                    'features' => ['8+ hours duration', 'Premium setup', 'Event manager', 'Valet parking', 'Cleanup service', 'Day-of coordination'],
                    'sort_order' => 3,
                ],
            ];

            foreach ($packages as $package) {
                $package['event_type_id'] = $eventType->id;
                EventPackage::create($package);
            }
        }

        $this->command->info('Event Types and Packages seeded successfully!');
    }
}
