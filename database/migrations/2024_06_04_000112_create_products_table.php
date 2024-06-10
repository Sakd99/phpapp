<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('product_name');
            $table->string('product_description');
            $table->string('product_price');
            $table->string('product_image1')->nullable();
            $table->string('product_image2')->nullable();
            $table->string('product_image3')->nullable();
            $table->string('prodeuct_discount')->nullable();
            $table->string('product_category');
            $table->string('product_stock');
            $table->string('product_status')->default('active');
            $table->string('product_rating')->nullable();
            $table->string('product_review')->nullable();
            $table->string('prodeuct_color')->nullable();
            $table->string('product_size')->nullable();
            $table->string('product_weight')->nullable();
            $table->string('product_dimension')->nullable();



        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
