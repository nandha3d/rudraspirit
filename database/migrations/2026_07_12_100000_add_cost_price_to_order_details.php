<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Profit engine (Plan A) — snapshot the product cost on each order line so
 * historical profit stays accurate even if the product cost changes later.
 *
 * Purely additive + guarded: safe to run on any DB, no-ops if the column exists.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('order_details') && !Schema::hasColumn('order_details', 'cost_price')) {
            Schema::table('order_details', function (Blueprint $table) {
                // total cost for the line (unit purchase_price * quantity) at sale time
                $table->decimal('cost_price', 20, 2)->default(0)->after('price');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('order_details') && Schema::hasColumn('order_details', 'cost_price')) {
            Schema::table('order_details', function (Blueprint $table) {
                $table->dropColumn('cost_price');
            });
        }
    }
};
