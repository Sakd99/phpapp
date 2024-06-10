<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // إنشاء 10 مستخدمين باستخدام المصنع
        User::factory(10)->create();

        // إنشاء مستخدم اختباري
        User::factory()->create([
            'name' => 'admin',
            'email' => 'super@mail.com',
            'password' => bcrypt('password'), // تأكد من تعيين كلمة مرور مشفرة
        ]);

        // إنشاء مستخدم إضافي
        User::factory()->create([
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'password' => bcrypt('password'), // تعيين كلمة مرور مشفرة
        ]);
    }
}
