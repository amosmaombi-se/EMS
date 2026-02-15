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
        Schema::create('vendors', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('vendor_category_id')->constrained()->onDelete('restrict');
            
            $table->string('business_name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('logo')->nullable();
            $table->string('cover_image')->nullable();
            
            $table->string('contact_person')->nullable();
            $table->string('email');
            $table->string('phone');
            $table->string('website')->nullable();
            
            $table->text('address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('country')->nullable();
            $table->string('postal_code')->nullable();
            
            $table->string('business_registration_number')->nullable();
            $table->string('tax_id')->nullable();
            
            $table->decimal('minimum_order_value', 10, 2)->default(0);
            $table->integer('years_of_experience')->default(0);
            $table->integer('team_size')->nullable();
            
            $table->json('service_areas')->nullable(); // Cities/regions they serve
            $table->json('specializations')->nullable();
            
            $table->decimal('rating', 3, 2)->default(0);
            $table->integer('total_reviews')->default(0);
            $table->integer('total_bookings')->default(0);
            
            $table->enum('verification_status', [
                'pending',
                'verified',
                'rejected'
            ])->default('pending');
            
            $table->timestamp('verified_at')->nullable();
            $table->text('verification_notes')->nullable();
            
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('slug');
            $table->index('vendor_category_id');
            $table->index('verification_status');
            $table->index(['is_active', 'is_featured']);
            $table->index(['city', 'state']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
