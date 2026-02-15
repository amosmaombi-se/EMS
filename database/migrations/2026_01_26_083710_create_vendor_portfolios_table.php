<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
  
    public function up(): void
    {
        Schema::create('vendor_portfolios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('vendor_id')->constrained()->onDelete('cascade');
            
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('image_url');
            $table->string('thumbnail_url')->nullable();
            $table->enum('media_type', ['image', 'video'])->default('image');
            
            $table->foreignId('event_type_id')->nullable()->constrained()->onDelete('set null');
            
            $table->integer('sort_order')->default(0);
            $table->boolean('is_featured')->default(false);
            
            $table->timestamps();

            $table->index('vendor_id');
            $table->index('event_type_id');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendor_portfolios');
    }
};
