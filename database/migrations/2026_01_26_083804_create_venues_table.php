<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('venues', function (Blueprint $table) {
                      $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            
            $table->enum('type', [
                'indoor',
                'outdoor',
                'banquet_hall',
                'garden',
                'rooftop',
                'beach',
                'hotel',
                'restaurant',
                'other'
            ]);
            
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('country');
            $table->string('postal_code')->nullable();
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            $table->string('contact_person');
            $table->string('email');
            $table->string('phone');
            
            $table->integer('capacity_min')->default(0);
            $table->integer('capacity_max');
            $table->decimal('area_sqft', 10, 2)->nullable();
            
            $table->decimal('base_price_per_day', 10, 2);
            $table->decimal('base_price_per_hour', 10, 2)->nullable();
            $table->decimal('security_deposit', 10, 2)->default(0);
            
            $table->json('amenities')->nullable(); // parking, wifi, ac, etc.
            $table->json('images')->nullable();
            $table->string('virtual_tour_url')->nullable();
            
            $table->text('terms_and_conditions')->nullable();
            $table->text('cancellation_policy')->nullable();
            
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_bookings')->default(0);
            
            $table->boolean('is_verified')->default(false);
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index(['city', 'state']);
            $table->index(['is_active', 'is_featured']);
            $table->index('type');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venues');
    }
};
