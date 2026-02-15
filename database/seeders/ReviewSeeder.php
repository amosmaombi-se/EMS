<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{
    Review,
    Booking,
    Vendor,
    Venue,
    User
};

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        $completedBookings = Booking::where('status', 'completed')->get();
        $vendors = Vendor::all();
        $venues = Venue::all();

        // Create reviews for vendors
        foreach ($vendors as $vendor) {
            $reviewCount = rand(5, 15);

            for ($i = 0; $i < $reviewCount; $i++) {
                $customer = User::whereHas('roles', function ($q) {
                    $q->where('slug', 'customer');
                })->inRandomOrder()->first();

                $rating = rand(3, 5);

                Review::create([
                    'user_id' => $customer->id,
                    'reviewable_type' => Vendor::class,
                    'reviewable_id' => $vendor->id,
                    'rating' => $rating,
                    'title' => $this->getReviewTitle($rating),
                    'comment' => $this->getReviewComment($rating, 'vendor'),
                    'status' => 'approved',
                    'is_verified_purchase' => rand(0, 1),
                    'helpful_count' => rand(0, 20),
                ]);
            }

            $vendor->updateRating();
        }

        // Create reviews for venues
        foreach ($venues as $venue) {
            $reviewCount = rand(10, 25);

            for ($i = 0; $i < $reviewCount; $i++) {
                $customer = User::whereHas('roles', function ($q) {
                    $q->where('slug', 'customer');
                })->inRandomOrder()->first();

                $rating = rand(3, 5);

                Review::create([
                    'user_id' => $customer->id,
                    'reviewable_type' => Venue::class,
                    'reviewable_id' => $venue->id,
                    'rating' => $rating,
                    'title' => $this->getReviewTitle($rating),
                    'comment' => $this->getReviewComment($rating, 'venue'),
                    'status' => 'approved',
                    'is_verified_purchase' => rand(0, 1),
                    'helpful_count' => rand(0, 30),
                ]);
            }

            $venue->updateRating();
        }

        $this->command->info('Reviews seeded successfully!');
    }

    private function getReviewTitle(int $rating): string
    {
        $titles = [
            5 => ['Excellent Service!', 'Absolutely Perfect!', 'Highly Recommended!', 'Outstanding Experience!', 'Beyond Expectations!'],
            4 => ['Very Good Service', 'Great Experience', 'Would Recommend', 'Professional Service', 'Very Satisfied'],
            3 => ['Good Service', 'Decent Experience', 'Average Service', 'Satisfactory', 'Met Expectations'],
        ];

        return $titles[$rating][array_rand($titles[$rating])];
    }

    private function getReviewComment(int $rating, string $type): string
    {
        $comments = [
            5 => [
                'vendor' => "Absolutely amazing service! The team was professional, punctual, and exceeded all our expectations. Would definitely hire again!",
                'venue' => "The venue was stunning and the staff were incredibly helpful. Everything was perfect and our guests were impressed!",
            ],
            4 => [
                'vendor' => "Great service overall. Very professional and delivered quality work. Minor improvements could be made but highly satisfied.",
                'venue' => "Beautiful venue with good facilities. The staff were friendly and accommodating. Would recommend to others.",
            ],
            3 => [
                'vendor' => "Service was good and met our basic requirements. Professional but nothing extraordinary. Fair value for money.",
                'venue' => "Decent venue for the price. Location is convenient and facilities are adequate. Some room for improvement.",
            ],
        ];

        return $comments[$rating][$type];
    }
}
