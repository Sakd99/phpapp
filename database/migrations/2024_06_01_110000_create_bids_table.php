<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('bids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('category_id');
            $table->string('product_name');
            $table->text('product_description');
            $table->decimal('initial_price', 8, 2);
            $table->decimal('current_price', 8, 2);
            $table->timestamp('end_time')->nullable();
            $table->string('product_image1')->nullable();
            $table->string('product_image2')->nullable();
            $table->string('product_image3')->nullable();
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bids');
    }
};
