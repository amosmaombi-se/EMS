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
        Schema::create('vendor_services', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            
            $table->string('name');
            $table->string('slug');
            $table->text('description')->nullable();
            
            $table->decimal('base_price', 10, 2);
            $table->enum('pricing_type', [
                'fixed',
                'per_person',
                'per_hour',
                'per_day',
                'custom'
            ])->default('fixed');
            
            $table->integer('min_quantity')->default(1);
            $table->integer('max_quantity')->nullable();
            
            $table->integer('duration_hours')->nullable();
            $table->json('features')->nullable();
            $table->json('images')->nullable();
            
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_services');
    }
};
