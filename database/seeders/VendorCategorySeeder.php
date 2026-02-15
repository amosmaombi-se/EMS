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
class VendorCategorySeeder extends Seeder
{
  public function run(): void
    {
        $categories = [
            ['name' => 'Catering Services', 'slug' => 'catering', 'icon' => '🍽️', 'sort_order' => 1],
            ['name' => 'Photography & Videography', 'slug' => 'photography', 'icon' => '📸', 'sort_order' => 2],
            ['name' => 'Decoration & Design', 'slug' => 'decoration', 'icon' => '🎨', 'sort_order' => 3],
            ['name' => 'DJ & Music', 'slug' => 'music', 'icon' => '🎵', 'sort_order' => 4],
            ['name' => 'Entertainment', 'slug' => 'entertainment', 'icon' => '🎭', 'sort_order' => 5],
            ['name' => 'Florists', 'slug' => 'florist', 'icon' => '💐', 'sort_order' => 6],
            ['name' => 'Makeup & Hair', 'slug' => 'makeup', 'icon' => '💄', 'sort_order' => 7],
            ['name' => 'Transportation', 'slug' => 'transportation', 'icon' => '🚗', 'sort_order' => 8],
            ['name' => 'Event Planning', 'slug' => 'planning', 'icon' => '📋', 'sort_order' => 9],
            ['name' => 'Invitation Cards', 'slug' => 'invitations', 'icon' => '💌', 'sort_order' => 10],
        ];

        foreach ($categories as $category) {
            VendorCategory::create([
                'name' => $category['name'],
                'slug' => $category['slug'],
                'description' => "Professional {$category['name']} for your events",
                'icon' => $category['icon'],
                'sort_order' => $category['sort_order'],
                'is_active' => true,
            ]);
        }

        $this->command->info('Vendor Categories seeded successfully!');
    }
}
