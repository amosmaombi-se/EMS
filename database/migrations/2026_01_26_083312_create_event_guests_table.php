<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('event_guests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('event_id')->constrained()->onDelete('cascade');
            
            // Personal Information
            $table->string('first_name', 100);
            $table->string('last_name', 100);
            $table->string('email', 255)->nullable();
            $table->string('phone', 20)->nullable();
            
            // Guest Classification
            $table->enum('category', [
                'vip', 
                'family', 
                'friends', 
                'colleagues', 
                'business', 
                'media', 
                'sponsors', 
                'other'
            ])->default('other');
            
            $table->enum('guest_type', [
                'primary',
                'plus_one',
                'child',
                'vendor',
                'staff',
                'speaker',
                'performer'
            ])->default('primary');
            
            $table->boolean('is_vip')->default(false);
            
            // RSVP Management
            $table->enum('rsvp_status', [
                'pending',
                'attending',
                'not_attending',
                'maybe'
            ])->default('pending');
            
            $table->integer('plus_ones')->default(0);
            $table->boolean('plus_one_allowed')->default(false);
            $table->timestamp('rsvp_responded_at')->nullable();
            $table->timestamp('rsvp_last_reminded_at')->nullable();
            
            // Dietary & Special Needs (keeping these as they're essential)
            $table->string('dietary_preference', 100)->nullable();
            $table->text('allergies')->nullable();
            $table->text('special_requirements')->nullable();
            $table->text('accessibility_needs')->nullable();
            $table->text('accommodation_needs')->nullable();
            $table->text('transportation_needs')->nullable();
            
            // Invitation Management
            $table->boolean('invitation_sent')->default(false);
            $table->timestamp('invitation_sent_at')->nullable();
            $table->enum('invitation_method', ['email', 'sms', 'whatsapp', 'printed'])->nullable();
            $table->text('invitation_link')->nullable();
            
            // Additional Information
            $table->string('language_preference', 50)->nullable()->default('en');
            $table->boolean('welcome_pack_sent')->default(false);
            $table->text('notes')->nullable();
            
            // Check-in/Check-out
            $table->timestamp('check_in_time')->nullable();
            $table->timestamp('check_out_time')->nullable();
            
            // Metadata
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index(['event_id', 'rsvp_status']);
            $table->index(['event_id', 'category']);
            $table->index(['event_id', 'is_vip']);
            $table->index(['event_id', 'invitation_sent']);
            $table->index('email');
            $table->unique(['event_id', 'email'], 'event_guest_email_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('event_guests');
    }
};