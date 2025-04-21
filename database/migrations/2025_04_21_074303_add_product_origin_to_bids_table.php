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
            $table->enum('product_origin', ['local', 'international'])->default('local')->after('product_condition')->comment('منشأ المنتج: محلي أو دولي');
            $table->string('origin_country')->nullable()->after('product_origin')->comment('اسم الدولة في حالة المنتج الدولي');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            $table->dropColumn(['product_origin', 'origin_country']);
        });
    }
};
