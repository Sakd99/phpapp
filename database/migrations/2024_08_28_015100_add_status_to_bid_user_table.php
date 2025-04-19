<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToBidUserTable extends Migration
{
    public function up()
    {
        if (Schema::hasTable('bid_user') && !Schema::hasColumn('bid_user', 'status')) {
            Schema::table('bid_user', function (Blueprint $table) {
                $table->string('status')->default('pending')->after('bid_amount');
            });
        }
    }

    public function down()
    {
        Schema::table('bid_user', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }
}
