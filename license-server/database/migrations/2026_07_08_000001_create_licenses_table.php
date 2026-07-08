<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('licenses', function (Blueprint $table) {
            $table->id();
            $table->string('license_key', 64)->unique();
            $table->string('product')->default('rudraspirit-engine')->index();
            $table->string('customer_name')->nullable();
            $table->string('customer_email')->nullable();
            // active | suspended | revoked
            $table->string('status', 20)->default('active')->index();
            // null = perpetual
            $table->timestamp('expires_at')->nullable();
            // how many distinct domains may activate this key
            $table->unsignedInteger('activation_limit')->default(1);
            $table->json('meta')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('licenses');
    }
};
