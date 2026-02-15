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
        Schema::create('reviews', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('booking_id')->nullable()->constrained()->onDelete('cascade');
            
            $table->string('reviewable_type'); // Vendor, Venue
            $table->unsignedBigInteger('reviewable_id');
            
            $table->tinyInteger('rating'); // 1-5
            $table->string('title')->nullable();
            $table->text('comment');
            
            $table->json('photos')->nullable();
            
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_response')->nullable();
            $table->timestamp('responded_at')->nullable();
            
            $table->boolean('is_verified_purchase')->default(false);
            $table->integer('helpful_count')->default(0);
            
            $table->timestamps();
            $table->softDeletes();

            $table->index(['reviewable_type', 'reviewable_id']);
            $table->index('user_id');
            $table->index('rating');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reviews');
    }
};
