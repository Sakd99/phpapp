<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRatingsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasTable('ratings')) {
            Schema::create('ratings', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('order_id');
                $table->unsignedBigInteger('buyer_id');
                $table->unsignedBigInteger('seller_id');
                $table->integer('rating')->default(0); // التقييم من 1 إلى 5
                $table->text('review')->nullable(); // مراجعة نصية
                $table->timestamps();

                // العلاقات
                $table->foreign('order_id')->references('id')->on('orders')->onDelete('cascade');
                $table->foreign('buyer_id')->references('id')->on('users')->onDelete('cascade');
                $table->foreign('seller_id')->references('id')->on('users')->onDelete('cascade');
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('ratings');
    }
}
