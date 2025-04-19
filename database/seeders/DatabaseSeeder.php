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
        User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => 'super@mail.com',
            'password' => bcrypt('password'),
        ]);

        User::factory()->create([
            'name' => 'zz',
            'email' => 'z@z.com',
            'password' => bcrypt('password'),
        ]);
    }
}
