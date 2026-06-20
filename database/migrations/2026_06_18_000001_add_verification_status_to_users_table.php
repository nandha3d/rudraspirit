<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddVerificationStatusToUsersTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('users', 'verification_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->tinyInteger('verification_status')->default(1);
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('users', 'verification_status')) {
            Schema::table('users', function (Blueprint $table) {
                $table->dropColumn('verification_status');
            });
        }
    }
}
