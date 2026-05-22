<?php

namespace Database\Seeders;

use App\Models\Tweet;
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

        if (! User::where('email', 'test@example.com')->exists()) {
            User::factory()->create([
                'name' => 'Test User',
                'email' => 'test@example.com',
                'link' => 'https://example.com',
                'link_text' => 'example.com',
                'location' => 'San Francisco, United States',
                'profile' => 'This is a test user account.',
            ]);
        }

        User::factory(9)->create();

        Tweet::factory(40)->create();

        Tweet::factory(20)->create(['user_id' => 1]);
    }
}
