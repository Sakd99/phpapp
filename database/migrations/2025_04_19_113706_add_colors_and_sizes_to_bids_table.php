<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->json('available_colors')->nullable()->comment('الألوان المتاحة للمنتج');
            $table->json('available_sizes')->nullable()->comment('الأحجام المتاحة للمنتج');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->dropColumn(['available_colors', 'available_sizes']);
        });
    }
};
