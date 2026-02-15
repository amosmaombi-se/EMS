<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Seeder;
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
class RolePermissionSeeder extends Seeder
{
   
   public function run(): void
    {
        // Create Permissions
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'group' => 'users'],
            ['name' => 'Create Users', 'slug' => 'create-users', 'group' => 'users'],
            ['name' => 'Edit Users', 'slug' => 'edit-users', 'group' => 'users'],
            ['name' => 'Delete Users', 'slug' => 'delete-users', 'group' => 'users'],
            
            // Event Management
            ['name' => 'View Events', 'slug' => 'view-events', 'group' => 'events'],
            ['name' => 'Create Events', 'slug' => 'create-events', 'group' => 'events'],
            ['name' => 'Edit Events', 'slug' => 'edit-events', 'group' => 'events'],
            ['name' => 'Delete Events', 'slug' => 'delete-events', 'group' => 'events'],
            ['name' => 'Manage All Events', 'slug' => 'manage-all-events', 'group' => 'events'],
            
            // Booking Management
            ['name' => 'View Bookings', 'slug' => 'view-bookings', 'group' => 'bookings'],
            ['name' => 'Create Bookings', 'slug' => 'create-bookings', 'group' => 'bookings'],
            ['name' => 'Edit Bookings', 'slug' => 'edit-bookings', 'group' => 'bookings'],
            ['name' => 'Cancel Bookings', 'slug' => 'cancel-bookings', 'group' => 'bookings'],
            ['name' => 'Manage All Bookings', 'slug' => 'manage-all-bookings', 'group' => 'bookings'],
            
            // Vendor Management
            ['name' => 'View Vendors', 'slug' => 'view-vendors', 'group' => 'vendors'],
            ['name' => 'Create Vendors', 'slug' => 'create-vendors', 'group' => 'vendors'],
            ['name' => 'Edit Vendors', 'slug' => 'edit-vendors', 'group' => 'vendors'],
            ['name' => 'Delete Vendors', 'slug' => 'delete-vendors', 'group' => 'vendors'],
            ['name' => 'Verify Vendors', 'slug' => 'verify-vendors', 'group' => 'vendors'],
            
            // Venue Management
            ['name' => 'View Venues', 'slug' => 'view-venues', 'group' => 'venues'],
            ['name' => 'Create Venues', 'slug' => 'create-venues', 'group' => 'venues'],
            ['name' => 'Edit Venues', 'slug' => 'edit-venues', 'group' => 'venues'],
            ['name' => 'Delete Venues', 'slug' => 'delete-venues', 'group' => 'venues'],
            
            // Payment Management
            ['name' => 'View Payments', 'slug' => 'view-payments', 'group' => 'payments'],
            ['name' => 'Process Payments', 'slug' => 'process-payments', 'group' => 'payments'],
            ['name' => 'Refund Payments', 'slug' => 'refund-payments', 'group' => 'payments'],
            
            // Review Management
            ['name' => 'View Reviews', 'slug' => 'view-reviews', 'group' => 'reviews'],
            ['name' => 'Approve Reviews', 'slug' => 'approve-reviews', 'group' => 'reviews'],
            ['name' => 'Delete Reviews', 'slug' => 'delete-reviews', 'group' => 'reviews'],
            
            // Report Management
            ['name' => 'View Reports', 'slug' => 'view-reports', 'group' => 'reports'],
            ['name' => 'Export Data', 'slug' => 'export-data', 'group' => 'reports'],
            
            // Settings
            ['name' => 'Manage Settings', 'slug' => 'manage-settings', 'group' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::create($permission);
        }

        // Create Roles
        $superAdmin = Role::create([
            'name' => 'Super Admin',
            'slug' => 'super-admin',
            'description' => 'Full system access',
            'is_active' => true,
        ]);

        $eventOrganizer = Role::create([
            'name' => 'Event Organizer',
            'slug' => 'event-organizer',
            'description' => 'Can create and manage events',
            'is_active' => true,
        ]);

        $vendor = Role::create([
            'name' => 'Vendor',
            'slug' => 'vendor',
            'description' => 'Service provider',
            'is_active' => true,
        ]);

        $customer = Role::create([
            'name' => 'Customer',
            'slug' => 'customer',
            'description' => 'Event customer',
            'is_active' => true,
        ]);

        $staff = Role::create([
            'name' => 'Staff',
            'slug' => 'staff',
            'description' => 'Team member',
            'is_active' => true,
        ]);

        // Assign Permissions to Super Admin (all permissions)
        $superAdmin->permissions()->attach(Permission::all());

        // Assign Permissions to Event Organizer
        $eventOrganizer->permissions()->attach(
            Permission::whereIn('slug', [
                'view-events', 'create-events', 'edit-events', 'delete-events',
                'view-bookings', 'create-bookings', 'edit-bookings', 'cancel-bookings',
                'view-vendors', 'view-venues',
                'view-payments', 'process-payments',
                'view-reports', 'export-data',
            ])->pluck('id')
        );

        // Assign Permissions to Vendor
        $vendor->permissions()->attach(
            Permission::whereIn('slug', [
                'view-vendors', 'edit-vendors',
                'view-bookings',
                'view-payments',
            ])->pluck('id')
        );

        // Assign Permissions to Customer
        $customer->permissions()->attach(
            Permission::whereIn('slug', [
                'view-events', 'create-events', 'edit-events',
                'view-bookings', 'create-bookings',
                'view-vendors', 'view-venues',
                'view-payments',
            ])->pluck('id')
        );

        // Assign Permissions to Staff
        $staff->permissions()->attach(
            Permission::whereIn('slug', [
                'view-events',
                'view-bookings',
                'view-vendors', 'view-venues',
            ])->pluck('id')
        );

        $this->command->info('Roles and Permissions seeded successfully!');
    }
}
