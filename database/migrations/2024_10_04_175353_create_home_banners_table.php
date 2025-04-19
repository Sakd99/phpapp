<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        if (!Schema::hasTable('home_banners')) {
            Schema::create('home_banners', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('category_id')->nullable();
                $table->unsignedBigInteger('subcategory_id')->nullable();
                $table->unsignedBigInteger('subsubcategory_id')->nullable();
                $table->string('image')->nullable();
                $table->integer('priority')->default(0);
                $table->timestamps();

                $table->foreign('category_id')->references('id')->on('categories')->onDelete('set null');
                $table->foreign('subcategory_id')->references('id')->on('sub_categories')->onDelete('set null');
                $table->foreign('subsubcategory_id')->references('id')->on('sub_categories')->onDelete('set null');
            });
        }
    }



    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('home_banners');
    }
};
