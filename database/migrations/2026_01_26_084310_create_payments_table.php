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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->string('payment_number')->unique(); // PAY-2024-00001
            $table->foreignId('booking_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            
            $table->decimal('amount', 10, 2);
            $table->string('currency')->default('USD');
            
            $table->enum('payment_method', [
                'cash',
                'card',
                'bank_transfer',
                'paypal',
                'stripe',
                'razorpay',
                'other'
            ]);
            
            $table->enum('status', [
                'pending',
                'processing',
                'completed',
                'failed',
                'refunded',
                'cancelled'
            ])->default('pending');
            
            $table->string('transaction_id')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->json('gateway_response')->nullable();
            
            $table->text('notes')->nullable();
            $table->timestamp('paid_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();

            $table->index('payment_number');
            $table->index('booking_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
