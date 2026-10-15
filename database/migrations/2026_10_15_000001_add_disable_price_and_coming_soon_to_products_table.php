<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            if (!Schema::hasColumn('products', 'disable_price')) {
                $table->boolean('disable_price')->default(0)->after('unit_price');
            }
            if (!Schema::hasColumn('products', 'coming_soon')) {
                $table->boolean('coming_soon')->default(0)->after('disable_price');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            if (Schema::hasColumn('products', 'disable_price')) {
                $table->dropColumn('disable_price');
            }
            if (Schema::hasColumn('products', 'coming_soon')) {
                $table->dropColumn('coming_soon');
            }
        });
    }
};
