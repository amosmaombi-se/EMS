<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('event_number')->unique(); // e.g., EVT-2024-00001
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Event owner/organizer
            $table->foreignId('event_type_id')->constrained()->onDelete('restrict');
            $table->foreignId('event_package_id')->nullable()->constrained()->onDelete('set null');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('event_date');
            $table->dateTime('event_end_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            
            $table->string('venue_name')->nullable();
            $table->text('venue_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            $table->integer('expected_guests')->default(0);
            $table->integer('confirmed_guests')->default(0);
            
            $table->decimal('estimated_budget', 12, 2)->default(0);
            $table->decimal('actual_cost', 12, 2)->default(0);
            
            $table->enum('status', [
                'draft',
                'planning',
                'confirmed',
                'ongoing',
                'completed',
                'cancelled'
            ])->default('draft');
            
            $table->text('notes')->nullable();
            $table->json('custom_fields')->nullable();
            $table->string('cover_image')->nullable();
            
            $table->boolean('is_public')->default(false);
            $table->boolean('is_recurring')->default(false);
            $table->string('recurrence_pattern')->nullable(); // daily, weekly, monthly
            
            $table->timestamp('published_at')->nullable();
            $table->timestamp('cancelled_at')->nullable();
            $table->text('cancellation_reason')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('event_number');
            $table->index('user_id');
            $table->index('event_type_id');
            $table->index('event_date');
            $table->index('status');
            $table->index(['city', 'state', 'country']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
