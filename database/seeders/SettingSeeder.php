<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Setting;
class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            ['key' => 'site_name', 'value' => 'Event Management System', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_description', 'value' => 'Complete event management solution', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_email', 'value' => 'info@eventmanagement.com', 'type' => 'string', 'group' => 'general'],
            ['key' => 'site_phone', 'value' => '+1 (555) 123-4567', 'type' => 'string', 'group' => 'general'],
            ['key' => 'timezone', 'value' => 'America/New_York', 'type' => 'string', 'group' => 'general'],
            ['key' => 'currency', 'value' => 'USD', 'type' => 'string', 'group' => 'general'],
            ['key' => 'date_format', 'value' => 'Y-m-d', 'type' => 'string', 'group' => 'general'],
            
            // Booking Settings
            ['key' => 'booking_advance_days', 'value' => '30', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'booking_cancellation_days', 'value' => '7', 'type' => 'integer', 'group' => 'booking'],
            ['key' => 'booking_confirmation_required', 'value' => '1', 'type' => 'boolean', 'group' => 'booking'],
            ['key' => 'allow_guest_booking', 'value' => '0', 'type' => 'boolean', 'group' => 'booking'],
            
            // Payment Settings
            ['key' => 'tax_rate', 'value' => '10', 'type' => 'integer', 'group' => 'payment'],
            ['key' => 'advance_payment_percentage', 'value' => '30', 'type' => 'integer', 'group' => 'payment'],
            ['key' => 'payment_due_days_before', 'value' => '7', 'type' => 'integer', 'group' => 'payment'],
            ['key' => 'enable_stripe', 'value' => '1', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'enable_paypal', 'value' => '1', 'type' => 'boolean', 'group' => 'payment'],
            ['key' => 'enable_bank_transfer', 'value' => '1', 'type' => 'boolean', 'group' => 'payment'],
            
            // Email Settings
            ['key' => 'send_booking_confirmation', 'value' => '1', 'type' => 'boolean', 'group' => 'email'],
            ['key' => 'send_payment_receipt', 'value' => '1', 'type' => 'boolean', 'group' => 'email'],
            ['key' => 'send_event_reminders', 'value' => '1', 'type' => 'boolean', 'group' => 'email'],
            ['key' => 'reminder_days_before', 'value' => '7', 'type' => 'integer', 'group' => 'email'],
            
            // Vendor Settings
            ['key' => 'vendor_verification_required', 'value' => '1', 'type' => 'boolean', 'group' => 'vendor'],
            ['key' => 'vendor_commission_percentage', 'value' => '10', 'type' => 'integer', 'group' => 'vendor'],
            ['key' => 'allow_vendor_registration', 'value' => '1', 'type' => 'boolean', 'group' => 'vendor'],
            
            // Review Settings
            ['key' => 'enable_reviews', 'value' => '1', 'type' => 'boolean', 'group' => 'review'],
            ['key' => 'review_moderation_required', 'value' => '1', 'type' => 'boolean', 'group' => 'review'],
            ['key' => 'min_rating', 'value' => '1', 'type' => 'integer', 'group' => 'review'],
            ['key' => 'max_rating', 'value' => '5', 'type' => 'integer', 'group' => 'review'],
            
            // Notification Settings
            ['key' => 'enable_email_notifications', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'enable_sms_notifications', 'value' => '0', 'type' => 'boolean', 'group' => 'notification'],
            ['key' => 'enable_push_notifications', 'value' => '1', 'type' => 'boolean', 'group' => 'notification'],
            
            // Security Settings
            ['key' => 'enable_two_factor', 'value' => '0', 'type' => 'boolean', 'group' => 'security'],
            ['key' => 'password_min_length', 'value' => '8', 'type' => 'integer', 'group' => 'security'],
            ['key' => 'session_timeout_minutes', 'value' => '120', 'type' => 'integer', 'group' => 'security'],
            
            // Feature Flags
            ['key' => 'enable_guest_list', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
            ['key' => 'enable_task_management', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
            ['key' => 'enable_budget_tracking', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
            ['key' => 'enable_messaging', 'value' => '1', 'type' => 'boolean', 'group' => 'features'],
        ];

        foreach ($settings as $setting) {
            Setting::create($setting);
        }

        $this->command->info('Settings seeded successfully!');
    }
}


