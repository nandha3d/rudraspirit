<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('license_addons', function (Blueprint $table) {
            $table->id();
            $table->foreignId('license_id')->constrained('licenses')->cascadeOnDelete();
            // Matches the addon `unique_identifier` in the client (e.g. affiliate_system)
            $table->string('addon_identifier')->index();
            $table->string('label')->nullable();
            // null = entitled for as long as the license itself is valid
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();

            $table->unique(['license_id', 'addon_identifier']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('license_addons');
    }
};
