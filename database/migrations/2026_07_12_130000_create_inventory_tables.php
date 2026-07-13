<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Purchase & Inventory (roadmap Plan E). Suppliers, purchase orders + items,
 * and a stock-movement ledger. Additive/new tables — existing stock columns
 * (product_stocks.qty, products.current_stock, products.purchase_price) are
 * only written when a PO is received.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('suppliers')) {
            Schema::create('suppliers', function (Blueprint $table) {
                $table->increments('id');
                $table->string('name');
                $table->string('email')->nullable();
                $table->string('phone')->nullable();
                $table->string('gstin')->nullable();
                $table->text('address')->nullable();
                $table->text('note')->nullable();
                $table->string('status', 16)->default('active');
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('purchase_orders')) {
            Schema::create('purchase_orders', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('supplier_id')->nullable();
                $table->string('reference')->nullable();
                $table->date('order_date')->nullable();
                $table->string('status', 16)->default('draft'); // draft | ordered | received | cancelled
                $table->decimal('total', 20, 2)->default(0);
                $table->text('note')->nullable();
                $table->dateTime('received_at')->nullable();
                $table->timestamps();
                $table->index('supplier_id');
                $table->index('status');
            });
        }

        if (!Schema::hasTable('purchase_order_items')) {
            Schema::create('purchase_order_items', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('purchase_order_id');
                $table->unsignedInteger('product_id');
                $table->string('variant')->nullable();
                $table->integer('qty')->default(0);
                $table->decimal('unit_cost', 20, 2)->default(0);
                $table->timestamps();
                $table->index('purchase_order_id');
                $table->index('product_id');
            });
        }

        if (!Schema::hasTable('stock_movements')) {
            Schema::create('stock_movements', function (Blueprint $table) {
                $table->increments('id');
                $table->unsignedInteger('product_id');
                $table->string('variant')->nullable();
                $table->string('type', 24)->default('adjustment'); // purchase | adjustment | correction
                $table->integer('qty'); // signed: +in / -out
                $table->string('reference')->nullable();
                $table->text('note')->nullable();
                $table->unsignedInteger('created_by')->nullable();
                $table->timestamps();
                $table->index('product_id');
                $table->index('type');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_movements');
        Schema::dropIfExists('purchase_order_items');
        Schema::dropIfExists('purchase_orders');
        Schema::dropIfExists('suppliers');
    }
};
