<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('pincodes')) {
            return;
        }

        Schema::create('pincodes', function (Blueprint $table) {
            $table->id();
            $table->string('pincode', 10)->index();
            $table->string('office_name')->nullable();
            $table->string('office_type', 20)->nullable();
            $table->string('district')->nullable();
            $table->string('state')->nullable();
            $table->string('circle')->nullable();
            $table->string('region')->nullable();
            $table->string('division')->nullable();
            $table->string('delivery', 40)->nullable();
            $table->decimal('latitude', 10, 6)->nullable();
            $table->decimal('longitude', 10, 6)->nullable();
            // Static reference data (~155k rows) — no timestamps to keep it lean.
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pincodes');
    }
};
