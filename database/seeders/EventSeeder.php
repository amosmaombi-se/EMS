<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\User;
use App\Models\EventType;
use Illuminate\Database\Seeder;
use App\Models\EventSchedule;
use App\Models\EventChecklist;
use App\Models\EventTask;
use App\Models\EventGuest;
class EventSeeder extends Seeder
{
    public function run(): void
    {
        $customers = User::whereHas('roles', function ($q) {
            $q->where('slug', 'customer');
        })->get();

        $eventTypes = EventType::all();
        $staff = User::whereHas('roles', function ($q) {
            $q->where('slug', 'staff');
        })->first();

        foreach ($customers as $customer) {
            // Create 1-2 events per customer
            $numberOfEvents = rand(1, 2);

            for ($e = 0; $e < $numberOfEvents; $e++) {
                $eventType = $eventTypes->random();
                $package = $eventType->packages->random();
                $eventDate = now()->addDays(rand(30, 180));

                $event = Event::create([
                    'user_id' => $customer->id,
                    'event_type_id' => $eventType->id,
                    'event_package_id' => $package->id,
                    'title' => "{$customer->first_name}'s {$eventType->name}",
                    'description' => "A wonderful {$eventType->name} celebration with family and friends.",
                    'event_date' => $eventDate,
                    'event_end_date' => $eventDate->copy()->addHours($eventType->typical_duration_hours),
                    'start_time' => '18:00:00',
                    'end_time' => '23:00:00',
                    'venue_name' => 'TBD',
                    'venue_address' => 'To be confirmed',
                    'city' => $customer->city,
                    'state' => $customer->state,
                    'country' => $customer->country,
                    'expected_guests' => rand(50, 200),
                    'confirmed_guests' => rand(30, 150),
                    'estimated_budget' => rand(5000, 15000),
                    'actual_cost' => rand(3000, 10000),
                    'status' => ['draft', 'planning', 'confirmed'][$e % 3],
                    'is_public' => rand(0, 1),
                    'published_at' => now(),
                ]);

                // Create Event Schedules
                $schedules = [
                    ['title' => 'Guest Arrival', 'duration' => 30],
                    ['title' => 'Welcome Speech', 'duration' => 15],
                    ['title' => 'Dinner Service', 'duration' => 90],
                    ['title' => 'Entertainment', 'duration' => 60],
                    ['title' => 'Cake Cutting', 'duration' => 30],
                    ['title' => 'Dancing', 'duration' => 120],
                ];

                $currentTime = $eventDate->copy()->setTimeFromTimeString($event->start_time);
                foreach ($schedules as $index => $scheduleData) {
                    $startTime = $currentTime->copy();
                    $endTime = $startTime->copy()->addMinutes($scheduleData['duration']);

                    EventSchedule::create([
                        'event_id' => $event->id,
                        'title' => $scheduleData['title'],
                        'description' => "Schedule for {$scheduleData['title']}",
                        'start_time' => $startTime,
                        'end_time' => $endTime,
                        'duration_minutes' => $scheduleData['duration'],
                        'sort_order' => $index + 1,
                    ]);

                    $currentTime = $endTime;
                }

                // Create Event Checklists and Tasks
                $checklist = EventChecklist::create([
                    'event_id' => $event->id,
                    'title' => 'Main Event Checklist',
                    'description' => 'Primary tasks for event planning',
                    'sort_order' => 1,
                ]);

                $tasks = [
                    ['title' => 'Book venue', 'priority' => 'high', 'days_before' => 90],
                    ['title' => 'Send invitations', 'priority' => 'high', 'days_before' => 60],
                    ['title' => 'Confirm catering', 'priority' => 'high', 'days_before' => 30],
                    ['title' => 'Hire photographer', 'priority' => 'medium', 'days_before' => 45],
                    ['title' => 'Arrange decorations', 'priority' => 'medium', 'days_before' => 30],
                    ['title' => 'Book entertainment', 'priority' => 'medium', 'days_before' => 45],
                    ['title' => 'Finalize guest list', 'priority' => 'high', 'days_before' => 20],
                    ['title' => 'Order cake', 'priority' => 'medium', 'days_before' => 15],
                    ['title' => 'Confirm all vendors', 'priority' => 'urgent', 'days_before' => 7],
                    ['title' => 'Final venue walkthrough', 'priority' => 'high', 'days_before' => 3],
                ];

                foreach ($tasks as $index => $taskData) {
                    $dueDate = $eventDate->copy()->subDays($taskData['days_before']);
                    $isCompleted = $dueDate->isPast() && rand(0, 10) > 3;

                    EventTask::create([
                        'event_id' => $event->id,
                        'event_checklist_id' => $checklist->id,
                        'assigned_to' => $staff->id,
                        'title' => $taskData['title'],
                        'description' => "Complete {$taskData['title']} for the event",
                        'priority' => $taskData['priority'],
                        'status' => $isCompleted ? 'completed' : 'pending',
                        'due_date' => $dueDate,
                        'completed_at' => $isCompleted ? $dueDate->copy()->addHours(rand(1, 24)) : null,
                        'completed_by' => $isCompleted ? $staff->id : null,
                        'sort_order' => $index + 1,
                    ]);
                }

                // Create Event Guests
                $guestNames = [
                    ['first_name' => 'John', 'last_name' => 'Doe', 'category' => 'family'],
                    ['first_name' => 'Jane', 'last_name' => 'Smith', 'category' => 'friends'],
                    ['first_name' => 'Robert', 'last_name' => 'Johnson', 'category' => 'family'],
                    ['first_name' => 'Emily', 'last_name' => 'Williams', 'category' => 'friends'],
                    ['first_name' => 'Michael', 'last_name' => 'Brown', 'category' => 'colleagues'],
                    ['first_name' => 'Sarah', 'last_name' => 'Davis', 'category' => 'vip'],
                    ['first_name' => 'David', 'last_name' => 'Miller', 'category' => 'family'],
                    ['first_name' => 'Lisa', 'last_name' => 'Wilson', 'category' => 'friends'],
                    ['first_name' => 'James', 'last_name' => 'Moore', 'category' => 'colleagues'],
                    ['first_name' => 'Maria', 'last_name' => 'Taylor', 'category' => 'friends'],
                ];

                foreach ($guestNames as $guestData) {
                    EventGuest::create([
                        'event_id' => $event->id,
                        'first_name' => $guestData['first_name'],
                        'last_name' => $guestData['last_name'],
                        'email' => strtolower($guestData['first_name'] . '.' . $guestData['last_name']) . '@example.com',
                        'phone' => '+1234' . rand(100000, 999999),
                        'category' => $guestData['category'],
                        'rsvp_status' => ['pending', 'attending', 'not_attending', 'maybe'][rand(0, 3)],
                        'plus_ones' => rand(0, 2),
                        'plus_one_allowed' => rand(0, 1),
                        'dietary_preference' => ['None', 'Vegetarian', 'Vegan', 'Gluten-Free'][rand(0, 3)],
                        'invitation_sent' => true,
                        'invitation_sent_at' => now()->subDays(rand(20, 60)),
                        'rsvp_responded_at' => rand(0, 1) ? now()->subDays(rand(1, 30)) : null,
                    ]);
                }
            }
        }

        $this->command->info('Events seeded successfully!');
    }
}  