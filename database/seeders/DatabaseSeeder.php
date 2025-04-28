<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create roles and permissions first
        $this->call(RolePermissionSeeder::class);
        
        // Then create users and assign them roles
        \App\Models\User::factory(10)->create()->each(function ($user) {
            // Assign a random role to each user
            $roles = ['admin', 'editor', 'viewer'];
            $user->assignRole($roles[array_rand($roles)]);
        });

        // Create sample data
        $this->call([
            SiteSeeder::class,
            // Add other seeders here
        ]);
    }
}
