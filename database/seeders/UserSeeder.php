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
class UserSeeder extends Seeder
{
      public function run(): void
    {
        // Super Admin
        $admin = User::create([
            'first_name' => 'Admin',
            'last_name' => 'User',
            'email' => 'maombibetets@gmail.com',
            'phone' => '255760607767',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'is_verified' => true,
            'city' => 'Dar es Salaam',
            'state' => 'Dar es Salaam',
            'country' => 'Tanzania',
        ]);
        $admin->roles()->attach(Role::where('slug', 'super-admin')->first());

        // Event Organizers
        $organizer1 = User::create([
            'first_name' => 'John',
            'last_name' => 'Organizer',
            'email' => 'organizer1@example.com',
            'phone' => '1234567891',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'is_verified' => true,
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
        ]);
        $organizer1->roles()->attach(Role::where('slug', 'event-organizer')->first());

        $organizer2 = User::create([
            'first_name' => 'Sarah',
            'last_name' => 'Williams',
            'email' => 'organizer2@example.com',
            'phone' => '1234567892',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'is_verified' => true,
            'city' => 'Los Angeles',
            'state' => 'CA',
            'country' => 'USA',
        ]);
        $organizer2->roles()->attach(Role::where('slug', 'event-organizer')->first());

        // Vendors
        $vendors = [
            ['first_name' => 'Mike', 'last_name' => 'Caterer', 'email' => 'vendor1@example.com', 'city' => 'New York'],
            ['first_name' => 'Lisa', 'last_name' => 'Photographer', 'email' => 'vendor2@example.com', 'city' => 'Los Angeles'],
            ['first_name' => 'David', 'last_name' => 'DJ', 'email' => 'vendor3@example.com', 'city' => 'Chicago'],
            ['first_name' => 'Emma', 'last_name' => 'Decorator', 'email' => 'vendor4@example.com', 'city' => 'Miami'],
        ];

        foreach ($vendors as $vendorData) {
            $vendor = User::create([
                'first_name' => $vendorData['first_name'],
                'last_name' => $vendorData['last_name'],
                'email' => $vendorData['email'],
                'phone' => '+123456789' . rand(0, 9),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'is_verified' => true,
                'city' => $vendorData['city'],
                'state' => 'NY',
                'country' => 'USA',
            ]);
            $vendor->roles()->attach(Role::where('slug', 'vendor')->first());
        }

        // Customers
        $customers = [
            ['first_name' => 'Alice', 'last_name' => 'Johnson', 'email' => 'customer1@example.com'],
            ['first_name' => 'Bob', 'last_name' => 'Smith', 'email' => 'customer2@example.com'],
            ['first_name' => 'Carol', 'last_name' => 'Davis', 'email' => 'customer3@example.com'],
            ['first_name' => 'Daniel', 'last_name' => 'Brown', 'email' => 'customer4@example.com'],
            ['first_name' => 'Eva', 'last_name' => 'Martinez', 'email' => 'customer5@example.com'],
        ];

        foreach ($customers as $customerData) {
            $customer = User::create([
                'first_name' => $customerData['first_name'],
                'last_name' => $customerData['last_name'],
                'email' => $customerData['email'],
                'phone' => '123456789' . rand(0, 9),
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
                'is_verified' => true,
                'city' => 'New York',
                'state' => 'NY',
                'country' => 'USA',
            ]);
            $customer->roles()->attach(Role::where('slug', 'customer')->first());
        }

        // Staff
        $staff = User::create([
            'first_name' => 'Tom',
            'last_name' => 'Staff',
            'email' => 'staff@example.com',
            'phone' => '+1234567899',
            'password' => Hash::make('password'),
            'email_verified_at' => now(),
            'is_active' => true,
            'is_verified' => true,
            'city' => 'New York',
            'state' => 'NY',
            'country' => 'USA',
        ]);
        $staff->roles()->attach(Role::where('slug', 'staff')->first());

        $this->command->info('Users seeded successfully!');
    }

}
