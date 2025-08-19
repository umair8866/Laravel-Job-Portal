<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'name' => 'Test User',
            'email' => 'admin@test.com',
            'password' => '12345678',
            'role' => 'admin',
            'email_verified_at' => now()
        ]);


        User::factory()->create([
            'name' => 'Test User',
            'email' => 'user@test.com',
            'password' => '12345678',
            'role' => 'user',
            'email_verified_at' => now()
        ]);

        \App\Models\Category::factory(5)->create();
        \App\Models\JobType::factory(5)->create();

    }
}
