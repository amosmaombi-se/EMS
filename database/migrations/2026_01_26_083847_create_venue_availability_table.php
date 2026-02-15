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
        Schema::create('EMS.venue_availabilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venue_id')->constrained()->onDelete('cascade');
            
            $table->date('date');
            $table->enum('status', ['available', 'booked', 'blocked'])->default('available');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->decimal('price_override', 10, 2)->nullable();
            $table->text('notes')->nullable();
            
            $table->timestamps();

            $table->unique(['venue_id', 'date']);
            $table->index(['venue_id', 'date', 'status']);

        });
    }

  
    public function down(): void
    {
        Schema::dropIfExists('EMS.venue_availabilities');
    }
};
