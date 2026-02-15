<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;


class DatabaseSeeder extends Seeder
{
    
    public function run(): void
    {
          $this->call([
            RolePermissionSeeder::class,
            UserSeeder::class,
            EventTypeSeeder::class,
            VendorCategorySeeder::class,
            VendorSeeder::class,
            VenueSeeder::class,
            EventSeeder::class,
            BookingSeeder::class,
            ReviewSeeder::class,
            SettingSeeder::class,
            EventGuestsSeeder::class,
        ]);

    }
}
