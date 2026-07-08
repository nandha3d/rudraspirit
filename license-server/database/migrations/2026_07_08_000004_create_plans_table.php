<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->decimal('price', 10, 2)->default(0);
            $table->string('currency', 10)->default('INR');
            // monthly | yearly | lifetime — display/billing hint
            $table->string('billing_period', 20)->default('yearly');
            // License validity issued from this plan. null = perpetual.
            $table->unsignedInteger('duration_days')->nullable();
            $table->unsignedInteger('activation_limit')->default(1);
            // Addon identifiers this plan entitles (matches client unique_identifier)
            $table->json('modules')->nullable();
            // Display bullet points for the pricing page
            $table->json('features')->nullable();
            // Optional external payment page (Razorpay/Stripe payment link etc.)
            $table->string('payment_link')->nullable();
            $table->boolean('is_featured')->default(false);
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('plans');
    }
};
