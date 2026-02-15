<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Venue;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\VenueAvailability;

class VenueSeeder extends Seeder
{
    public function run(): void
    {
        $venueOwners = User::whereHas('roles', function ($q) {
            $q->where('slug', 'event-organizer');
        })->get();

        $venues = [
            [
                'name' => 'Grand Ballroom Hotel',
                'type' => 'banquet_hall',
                'city' => 'New York',
                'capacity_min' => 100,
                'capacity_max' => 500,
                'base_price_per_day' => 5000,
                'description' => 'Elegant ballroom with crystal chandeliers and state-of-the-art facilities. Perfect for weddings and corporate events.',
                'amenities' => ['Parking', 'WiFi', 'Air Conditioning', 'Sound System', 'Stage', 'Kitchen', 'Bridal Suite', 'Valet Service'],
            ],
            [
                'name' => 'Sunset Garden Venue',
                'type' => 'garden',
                'city' => 'Los Angeles',
                'capacity_min' => 50,
                'capacity_max' => 300,
                'base_price_per_day' => 3500,
                'description' => 'Beautiful outdoor garden venue with stunning sunset views. Ideal for intimate weddings and garden parties.',
                'amenities' => ['Parking', 'WiFi', 'Outdoor Seating', 'Gazebo', 'Lighting', 'Restrooms', 'Backup Indoor Space'],
            ],
            [
                'name' => 'Metropolitan Conference Center',
                'type' => 'indoor',
                'city' => 'Chicago',
                'capacity_min' => 200,
                'capacity_max' => 1000,
                'base_price_per_day' => 8000,
                'description' => 'Modern conference center with multiple halls and breakout rooms. Perfect for corporate events and large conferences.',
                'amenities' => ['Parking', 'WiFi', 'Air Conditioning', 'AV Equipment', 'Stage', 'Multiple Rooms', 'Catering Kitchen', 'Registration Area'],
            ],
            [
                'name' => 'Beachside Resort Pavilion',
                'type' => 'beach',
                'city' => 'Miami',
                'capacity_min' => 75,
                'capacity_max' => 250,
                'base_price_per_day' => 4500,
                'description' => 'Stunning beachfront pavilion with ocean views. Perfect for destination weddings and beach parties.',
                'amenities' => ['Beach Access', 'Parking', 'WiFi', 'Sound System', 'Lighting', 'Restrooms', 'Bar Area', 'Covered Pavilion'],
            ],
            [
                'name' => 'Skyline Rooftop Venue',
                'type' => 'rooftop',
                'city' => 'New York',
                'capacity_min' => 50,
                'capacity_max' => 200,
                'base_price_per_day' => 4000,
                'description' => 'Exclusive rooftop venue with panoramic city views. Ideal for cocktail parties and corporate events.',
                'amenities' => ['Elevator Access', 'WiFi', 'Bar', 'Lounge Seating', 'Heating Lamps', 'Sound System', 'City Views'],
            ],
            [
                'name' => 'Historic Manor Estate',
                'type' => 'indoor',
                'city' => 'Boston',
                'capacity_min' => 80,
                'capacity_max' => 300,
                'base_price_per_day' => 6000,
                'description' => 'Elegant historic manor with beautiful architecture and manicured gardens. Perfect for classic weddings.',
                'amenities' => ['Parking', 'WiFi', 'Gardens', 'Grand Staircase', 'Multiple Rooms', 'Bridal Suite', 'Catering Kitchen'],
            ],
        ];

        foreach ($venues as $index => $venueData) {
            $owner = $venueOwners->random();

            $venue = Venue::create([
                'user_id' => $owner->id,
                'name' => $venueData['name'],
                'slug' => Str::slug($venueData['name']),
                'description' => $venueData['description'],
                'type' => $venueData['type'],
                'address' => rand(100, 9999) . ' Event Boulevard',
                'city' => $venueData['city'],
                'state' => 'NY',
                'country' => 'USA',
                'postal_code' => rand(10000, 99999),
                'latitude' => rand(4000, 4100) / 100,
                'longitude' => rand(-7400, -7300) / 100,
                'contact_person' => $owner->full_name,
                'email' => $owner->email,
                'phone' => $owner->phone,
                'capacity_min' => $venueData['capacity_min'],
                'capacity_max' => $venueData['capacity_max'],
                'area_sqft' => rand(3000, 10000),
                'base_price_per_day' => $venueData['base_price_per_day'],
                'base_price_per_hour' => $venueData['base_price_per_day'] / 8,
                'security_deposit' => $venueData['base_price_per_day'] * 0.2,
                'amenities' => $venueData['amenities'],
                'images' => [
                    "https://picsum.photos/1200/800?random=" . ($index * 10 + 1),
                    "https://picsum.photos/1200/800?random=" . ($index * 10 + 2),
                    "https://picsum.photos/1200/800?random=" . ($index * 10 + 3),
                    "https://picsum.photos/1200/800?random=" . ($index * 10 + 4),
                ],
                'rating' => rand(40, 50) / 10,
                'total_reviews' => rand(20, 100),
                'total_bookings' => rand(50, 300),
                'is_verified' => true,
                'is_featured' => rand(0, 1),
                'is_active' => true,
            ]);

            // Create Availability (next 180 days)
            for ($i = 0; $i < 180; $i++) {
                $date = now()->addDays($i);
                $isBooked = rand(0, 10) > 6; // 40% chance of being booked

                VenueAvailability::create([
                    'venue_id' => $venue->id,
                    'date' => $date,
                    'status' => $isBooked ? 'booked' : 'available',
                    'price_override' => rand(0, 10) > 7 ? $venueData['base_price_per_day'] * 1.2 : null, // 30% chance of premium pricing
                ]);
            }
        }

        $this->command->info('Venues seeded successfully!');
    }
}
