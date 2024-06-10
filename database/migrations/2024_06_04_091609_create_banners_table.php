<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('banner_image')->nullable();
            $table->string('banner_title');
            $table->string('banner_address');
            $table->string('banner_description');
            $table->string('banner_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('banners');
    }
};
