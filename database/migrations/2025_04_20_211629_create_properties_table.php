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
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // اسم الخاصية
            $table->string('type')->default('text'); // نوع الخاصية (نص، رقم، الخ)
            $table->text('options')->nullable(); // الخيارات المتاحة (مثل الألوان أو الأحجام)
            $table->boolean('is_required')->default(false); // هل الخاصية مطلوبة
            $table->boolean('is_active')->default(true); // هل الخاصية نشطة
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
