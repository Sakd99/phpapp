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
            // حذف الأعمدة القديمة
            $table->dropColumn(['origin_country']);

            // تعديل عمود product_origin ليكون نصياً بدلاً من enum
            $table->string('product_origin')->comment('منشأ المنتج (اسم الدولة)')->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('bids', function (Blueprint $table) {
            // إعادة الأعمدة إلى حالتها السابقة
            $table->enum('product_origin', ['local', 'international'])->default('local')->comment('منشأ المنتج: محلي أو دولي')->change();
            $table->string('origin_country')->nullable()->comment('اسم الدولة في حالة المنتج الدولي')->after('product_origin');
        });
    }
};
