<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guest_checkins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guest_id')->constrained('event_guests')->onDelete('cascade');
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            
            // Check-in Information
            $table->timestamp('check_in_time');
            $table->timestamp('check_out_time')->nullable();
            
            // Check-in Method
            $table->enum('method', [
                'qr_code',
                'manual',
                'facial_recognition',
                'rfid',
                'mobile_app',
                'kiosk'
            ])->default('manual');
            
            $table->string('check_in_code', 100)->nullable()->comment('QR code or check-in token');
            $table->string('device_id', 100)->nullable()->comment('Device used for check-in');
            $table->string('location', 100)->nullable()->comment('Check-in location/station');
            
            // Guest Information at Check-in
            $table->integer('total_guests')->default(1)->comment('Total guests checked in (including +1s)');
            $table->json('checked_in_guests')->nullable()->comment('Names of all guests checked in');
            
            // Welcome Pack & Items
            $table->boolean('welcome_pack_given')->default(false);
            $table->string('welcome_pack_items', 255)->nullable();
            $table->boolean('badge_given')->default(false);
            $table->string('badge_number', 50)->nullable();
            
            // Additional Information
            $table->text('notes')->nullable();
            $table->foreignId('checked_in_by')->nullable()->constrained('users')->nullOnDelete();
            
            $table->timestamps();
            
            // Indexes
            $table->index(['guest_id', 'check_in_time']);
            $table->index(['event_id', 'check_in_time']);
            $table->index('check_in_code');
            $table->index('method');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guest_checkins');
    }
};