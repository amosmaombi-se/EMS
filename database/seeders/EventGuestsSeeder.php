<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class EventGuestsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $guestData = [];
        $now = Carbon::now();
        
        // Categories and types for random assignment
        $categories = ['vip', 'family', 'friends', 'colleagues', 'business', 'media', 'sponsors', 'other'];
        $guestTypes = ['primary', 'plus_one', 'child', 'vendor', 'staff', 'speaker', 'performer'];
        $rsvpStatuses = ['pending', 'attending', 'not_attending', 'maybe'];
        $invitationMethods = ['email', 'sms', 'whatsapp', 'printed', null];
        $languages = ['en', 'es', 'fr', 'de', 'it'];
        
        // Sample names for realistic data
        $firstNames = ['James', 'Mary', 'John', 'Patricia', 'Robert', 'Jennifer', 'Michael', 'Linda', 'William', 'Elizabeth', 
                      'David', 'Susan', 'Richard', 'Jessica', 'Joseph', 'Sarah', 'Thomas', 'Karen', 'Charles', 'Nancy'];
        $lastNames = ['Smith', 'Johnson', 'Williams', 'Brown', 'Jones', 'Garcia', 'Miller', 'Davis', 'Rodriguez', 'Martinez',
                     'Hernandez', 'Lopez', 'Gonzalez', 'Wilson', 'Anderson', 'Thomas', 'Taylor', 'Moore', 'Jackson', 'Martin'];

        // Generate 100-150 guests per event
        for ($eventId = 1; $eventId <= 6; $eventId++) {
            $guestsPerEvent = rand(100, 150);
            
            for ($i = 1; $i <= $guestsPerEvent; $i++) {
                $firstName = $firstNames[array_rand($firstNames)];
                $lastName = $lastNames[array_rand($lastNames)];
                $category = $categories[array_rand($categories)];
                $isVip = ($category === 'vip' || rand(1, 10) === 1);
                
                $guestType = $guestTypes[array_rand($guestTypes)];
                $plusOneAllowed = ($guestType === 'primary' && rand(0, 1));
                $plusOnes = $plusOneAllowed ? rand(0, 3) : 0;
                
                // Determine RSVP status with realistic probabilities
                $rsvpStatus = $rsvpStatuses[array_rand($rsvpStatuses)];
                $rsvpRespondedAt = null;
                if ($rsvpStatus !== 'pending' && rand(0, 1)) {
                    $rsvpRespondedAt = $now->copy()->subDays(rand(1, 30));
                }
                
                // Dietary preferences (20% of guests)
                $dietaryPreferences = [null, 'Vegetarian', 'Vegan', 'Gluten-Free', 'Dairy-Free', 'Kosher', 'Halal'];
                $dietaryPreference = rand(0, 4) === 0 ? $dietaryPreferences[array_rand($dietaryPreferences)] : null;
                
                // Invitation sent (80% of guests)
                $invitationSent = rand(0, 4) !== 0;
                $invitationSentAt = $invitationSent ? $now->copy()->subDays(rand(10, 60)) : null;
                $invitationMethod = $invitationSent ? $invitationMethods[array_rand($invitationMethods)] : null;
                
                // Check-in for some guests (30% of attending guests)
                $checkInTime = null;
                if ($rsvpStatus === 'attending' && rand(0, 2) === 0) {
                    $checkInTime = $now->copy()->subHours(rand(1, 48));
                    $checkOutTime = rand(0, 1) ? $checkInTime->copy()->addHours(rand(2, 8)) : null;
                }
                
                $guestData[] = [
                    'event_id' => $eventId,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'email' => Str::lower($firstName . '.' . $lastName . rand(1, 999) . '@example.com'),
                    'phone' => '+1' . rand(200, 999) . rand(200, 999) . rand(1000, 9999),
                    'category' => $category,
                    'guest_type' => $guestType,
                    'is_vip' => $isVip,
                    'rsvp_status' => $rsvpStatus,
                    'plus_ones' => $plusOnes,
                    'plus_one_allowed' => $plusOneAllowed,
                    'rsvp_responded_at' => $rsvpRespondedAt,
                    'rsvp_last_reminded_at' => rand(0, 1) ? $now->copy()->subDays(rand(1, 7)) : null,
                    'dietary_preference' => $dietaryPreference,
                    'allergies' => rand(0, 9) === 0 ? 'Peanuts, Shellfish' : null,
                    'special_requirements' => rand(0, 9) === 0 ? 'Requires wheelchair access' : null,
                    'accessibility_needs' => rand(0, 9) === 0 ? 'Hearing impaired - requires sign language interpreter' : null,
                    'accommodation_needs' => rand(0, 19) === 0 ? 'Requires hotel booking assistance' : null,
                    'transportation_needs' => rand(0, 19) === 0 ? 'Airport pickup required' : null,
                    'invitation_sent' => $invitationSent,
                    'invitation_sent_at' => $invitationSentAt,
                    'invitation_method' => $invitationMethod,
                    'invitation_link' => $invitationSent ? 'https://event-invite.com/' . Str::random(20) : null,
                    'language_preference' => $languages[array_rand($languages)],
                    'welcome_pack_sent' => rand(0, 2) === 0,
                    'notes' => rand(0, 4) === 0 ? 'Important client - ensure VIP treatment' : null,
                    'check_in_time' => $checkInTime,
                    'check_out_time' => isset($checkOutTime) ? $checkOutTime : null,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
                
                // Insert in batches to improve performance
                if (count($guestData) >= 100) {
                    DB::table('event_guests')->insert($guestData);
                    $guestData = [];
                }
            }
        }
        
        // Insert any remaining records
        if (!empty($guestData)) {
            DB::table('event_guests')->insert($guestData);
        }
        
        $this->command->info('Event guests seeded successfully!');
        $this->command->info('Total guests created: ' . DB::table('event_guests')->count());
    }
}