<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBidUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('bid_user')) {
            Schema::create('bid_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('bid_id');
                $table->unsignedBigInteger('user_id');
                $table->decimal('bid_amount', 10, 2);
                $table->timestamps();

                $table->foreign('bid_id')->references('id')->on('bids')->onDelete('cascade');
                $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bid_user');
    }
}
