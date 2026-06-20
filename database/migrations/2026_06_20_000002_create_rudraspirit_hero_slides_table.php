<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('rudraspirit_hero_slides')) {
            return;
        }

        Schema::create('rudraspirit_hero_slides', function (Blueprint $table) {
            $table->id();
            $table->string('image')->nullable(); // upload id (uploaded_asset)
            $table->string('kicker')->nullable();
            $table->string('title')->nullable();
            $table->string('title_em')->nullable();
            $table->text('text')->nullable();
            $table->string('cta_text')->nullable();
            $table->string('cta_link')->nullable();
            $table->integer('sort_order')->default(0);
            $table->boolean('status')->default(1);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rudraspirit_hero_slides');
    }
};
