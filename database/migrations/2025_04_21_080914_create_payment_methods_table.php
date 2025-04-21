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
        Schema::create('payment_methods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('type'); // نوع طريقة الدفع (مثل zaincash, qicard, إلخ)
            $table->string('account_number'); // رقم الحساب أو رقم الهاتف
            $table->boolean('is_default')->default(false); // هل هذه طريقة الدفع الافتراضية
            $table->boolean('is_active')->default(true); // هل طريقة الدفع نشطة
            $table->timestamps();

            // إنشاء فهرس مركب لضمان عدم تكرار نفس النوع لنفس المستخدم
            $table->unique(['user_id', 'type', 'account_number']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payment_methods');
    }
};
