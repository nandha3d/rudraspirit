<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Basic Accounting (Plan B, Phase 1): expense categories, financial accounts,
 * and expenses. Fully additive/new tables — no existing table touched.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('expense_categories')) {
            Schema::create('expense_categories', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('financial_accounts')) {
            Schema::create('financial_accounts', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('type', 32)->default('cash'); // cash | bank | wallet | gateway | other
                $table->decimal('opening_balance', 20, 2)->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('expenses')) {
            Schema::create('expenses', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('expense_category_id')->nullable();
                $table->unsignedInteger('financial_account_id')->nullable();
                $table->decimal('amount', 20, 2)->default(0);
                $table->decimal('tax', 20, 2)->default(0);
                $table->date('date')->nullable();
                $table->string('payee')->nullable();
                $table->string('reference')->nullable();
                $table->string('attachment')->nullable();
                $table->text('note')->nullable();
                $table->timestamps();
                $table->index('expense_category_id');
                $table->index('date');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
        Schema::dropIfExists('financial_accounts');
        Schema::dropIfExists('expense_categories');
    }
};
