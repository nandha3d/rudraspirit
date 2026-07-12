<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Partner profit-share (Plan C). Partners get their own login and a read-only
 * dashboard; admin runs profit distributions per period and marks payouts.
 * Additive/new tables + a new auth guard — no existing table touched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('partners')) {
            Schema::create('partners', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->unique();
                $table->string('phone')->nullable();
                $table->string('password');
                $table->decimal('share_percent', 8, 2)->default(0); // % of net profit
                $table->string('status', 16)->default('active');    // active | inactive
                $table->text('note')->nullable();
                $table->rememberToken();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('partner_distributions')) {
            Schema::create('partner_distributions', function (Blueprint $table) {
                $table->increments('id');
                $table->date('period_from');
                $table->date('period_to');
                $table->decimal('net_profit', 20, 2)->default(0);
                $table->string('status', 16)->default('draft');     // draft | finalized
                $table->text('note')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('partner_distribution_shares')) {
            Schema::create('partner_distribution_shares', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('partner_distribution_id');
                $table->unsignedInteger('partner_id');
                $table->decimal('share_percent', 8, 2)->default(0);
                $table->decimal('amount', 20, 2)->default(0);
                $table->boolean('paid')->default(false);
                $table->dateTime('paid_at')->nullable();
                $table->string('method')->nullable();
                $table->string('reference')->nullable();
                $table->timestamps();
                $table->index('partner_distribution_id');
                $table->index('partner_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('partner_distribution_shares');
        Schema::dropIfExists('partner_distributions');
        Schema::dropIfExists('partners');
    }
};
