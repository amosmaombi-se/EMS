<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

class VendorSeeder extends Seeder
{
    public function run(): void
    {
        $vendorUsers = User::whereHas('roles', function ($q) {
            $q->where('slug', 'vendor');
        })->get();

        $categories = VendorCategory::all();
        $eventTypes = EventType::all();

        $vendorData = [
            [
                'business_name' => 'Gourmet Delights Catering',
                'category' => 'catering',
                'description' => 'Premium catering services for all types of events. We specialize in fusion cuisine and can accommodate dietary restrictions.',
                'years_of_experience' => 15,
                'team_size' => 25,
                'minimum_order_value' => 500,
            ],
            [
                'business_name' => 'Perfect Moments Photography',
                'category' => 'photography',
                'description' => 'Award-winning photography and videography services. We capture the essence of your special moments with artistic flair.',
                'years_of_experience' => 10,
                'team_size' => 8,
                'minimum_order_value' => 800,
            ],
            [
                'business_name' => 'Elite Events DJ Services',
                'category' => 'music',
                'description' => 'Professional DJ services with state-of-the-art equipment. We keep your party alive with the perfect music selection.',
                'years_of_experience' => 12,
                'team_size' => 5,
                'minimum_order_value' => 400,
            ],
            [
                'business_name' => 'Elegant Decorations Co.',
                'category' => 'decoration',
                'description' => 'Transform your venue into a magical space. We specialize in themed decorations and custom designs.',
                'years_of_experience' => 8,
                'team_size' => 15,
                'minimum_order_value' => 600,
            ],
        ];

        foreach ($vendorUsers as $index => $user) {
            if (!isset($vendorData[$index])) break;

            $data = $vendorData[$index];
            $category = $categories->where('slug', $data['category'])->first();

            $vendor = Vendor::create([
                'user_id' => $user->id,
                'vendor_category_id' => $category->id,
                'business_name' => $data['business_name'],
                'slug' => Str::slug($data['business_name']),
                'description' => $data['description'],
                'email' => $user->email,
                'phone' => $user->phone,
                'contact_person' => $user->full_name,
                'city' => $user->city,
                'state' => $user->state,
                'country' => $user->country,
                'address' => rand(100, 999) . ' Main Street',
                'years_of_experience' => $data['years_of_experience'],
                'team_size' => $data['team_size'],
                'minimum_order_value' => $data['minimum_order_value'],
                'service_areas' => ['New York', 'Brooklyn', 'Queens', 'Manhattan'],
                'specializations' => ['Weddings', 'Corporate Events', 'Private Parties'],
                'rating' => rand(40, 50) / 10,
                'total_reviews' => rand(10, 50),
                'total_bookings' => rand(50, 200),
                'verification_status' => 'verified',
                'verified_at' => now()->subDays(rand(30, 365)),
                'is_featured' => rand(0, 1),
                'is_active' => true,
            ]);

            // Create Services for each vendor
            $services = $this->getServicesForCategory($data['category']);
            foreach ($services as $service) {
                VendorService::create([
                    'vendor_id' => $vendor->id,
                    'name' => $service['name'],
                    'slug' => Str::slug($service['name']),
                    'description' => $service['description'],
                    'base_price' => $service['base_price'],
                    'pricing_type' => $service['pricing_type'],
                    'duration_hours' => $service['duration_hours'] ?? null,
                    'features' => $service['features'],
                    'is_active' => true,
                ]);
            }

            // Create Availability (next 90 days)
            for ($i = 0; $i < 90; $i++) {
                $date = now()->addDays($i);
                $isBooked = rand(0, 10) > 7; // 30% chance of being booked

                VendorAvailability::create([
                    'vendor_id' => $vendor->id,
                    'date' => $date,
                    'status' => $isBooked ? 'booked' : 'available',
                ]);
            }

            // Create Portfolio Items
            for ($i = 1; $i <= 5; $i++) {
                VendorPortfolio::create([
                    'vendor_id' => $vendor->id,
                    'title' => "Project {$i} - {$data['business_name']}",
                    'description' => "One of our finest works showcasing our expertise in {$category->name}",
                    'image_url' => "https://picsum.photos/800/600?random=" . ($vendor->id * 10 + $i),
                    'thumbnail_url' => "https://picsum.photos/400/300?random=" . ($vendor->id * 10 + $i),
                    'media_type' => 'image',
                    'event_type_id' => $eventTypes->random()->id,
                    'sort_order' => $i,
                    'is_featured' => $i <= 2,
                ]);
            }
        }

        $this->command->info('Vendors seeded successfully!');
    }

    private function getServicesForCategory(string $category): array
    {
        $services = [
            'catering' => [
                [
                    'name' => 'Buffet Service',
                    'description' => 'All-you-can-eat buffet with multiple cuisine options',
                    'base_price' => 35,
                    'pricing_type' => 'per_person',
                    'features' => ['3-course meal', 'Unlimited servings', 'Professional staff', 'Setup and cleanup'],
                ],
                [
                    'name' => 'Plated Dinner Service',
                    'description' => 'Elegant plated dinner service',
                    'base_price' => 50,
                    'pricing_type' => 'per_person',
                    'features' => ['4-course meal', 'Table service', 'Professional waitstaff', 'Premium presentation'],
                ],
                [
                    'name' => 'Cocktail Hour Package',
                    'description' => 'Appetizers and drinks service',
                    'base_price' => 25,
                    'pricing_type' => 'per_person',
                    'features' => ['Variety of appetizers', 'Beverage service', '2-hour duration'],
                ],
            ],
            'photography' => [
                [
                    'name' => 'Full Day Coverage',
                    'description' => 'Complete photography coverage for your entire event',
                    'base_price' => 1500,
                    'pricing_type' => 'fixed',
                    'duration_hours' => 8,
                    'features' => ['8 hours coverage', '500+ edited photos', 'Online gallery', 'Second photographer'],
                ],
                [
                    'name' => 'Half Day Package',
                    'description' => '4 hours of professional photography',
                    'base_price' => 800,
                    'pricing_type' => 'fixed',
                    'duration_hours' => 4,
                    'features' => ['4 hours coverage', '200+ edited photos', 'Online gallery'],
                ],
                [
                    'name' => 'Video Package',
                    'description' => 'Professional videography service',
                    'base_price' => 1200,
                    'pricing_type' => 'fixed',
                    'duration_hours' => 8,
                    'features' => ['Full day coverage', 'Edited highlight reel', 'Raw footage', 'Drone shots'],
                ],
            ],
            'music' => [
                [
                    'name' => 'DJ Service - 4 Hours',
                    'description' => 'Professional DJ with sound system',
                    'base_price' => 600,
                    'pricing_type' => 'fixed',
                    'duration_hours' => 4,
                    'features' => ['Professional sound system', 'Lighting effects', 'Music mixing', 'MC services'],
                ],
                [
                    'name' => 'DJ Service - 6 Hours',
                    'description' => 'Extended DJ service with premium equipment',
                    'base_price' => 900,
                    'pricing_type' => 'fixed',
                    'duration_hours' => 6,
                    'features' => ['Premium sound system', 'Advanced lighting', 'Music mixing', 'MC services', 'Song requests'],
                ],
                [
                    'name' => 'DJ + Photo Booth',
                    'description' => 'DJ service with photo booth',
                    'base_price' => 1200,
                    'pricing_type' => 'fixed',
                    'duration_hours' => 4,
                    'features' => ['DJ service', 'Photo booth', 'Props', 'Instant prints', 'Digital copies'],
                ],
            ],
            'decoration' => [
                [
                    'name' => 'Basic Decoration Package',
                    'description' => 'Essential decorations for your event',
                    'base_price' => 800,
                    'pricing_type' => 'fixed',
                    'features' => ['Table centerpieces', 'Chair covers', 'Entrance decoration', 'Setup and breakdown'],
                ],
                [
                    'name' => 'Premium Decoration Package',
                    'description' => 'Elaborate decorations with custom theme',
                    'base_price' => 1500,
                    'pricing_type' => 'fixed',
                    'features' => ['Custom theme design', 'Floral arrangements', 'Stage decoration', 'Lighting effects', 'Setup and breakdown'],
                ],
                [
                    'name' => 'Luxury Decoration Package',
                    'description' => 'Complete venue transformation',
                    'base_price' => 3000,
                    'pricing_type' => 'fixed',
                    'features' => ['Full venue transformation', 'Premium floral arrangements', 'Custom backdrops', 'Advanced lighting', 'Draping', 'Setup and breakdown'],
                ],
            ],
        ];

        return $services[$category] ?? [];
    }
}
