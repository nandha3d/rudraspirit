<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique();
            $table->foreignId('plan_id')->constrained('plans');
            $table->string('customer_name');
            $table->string('customer_email');
            $table->string('domain')->nullable();
            // Snapshot of the plan price at order time
            $table->decimal('amount', 10, 2)->default(0);
            $table->string('currency', 10)->default('INR');
            // pending | paid | issued | cancelled
            $table->string('status', 20)->default('pending')->index();
            $table->foreignId('license_id')->nullable()->constrained('licenses')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
