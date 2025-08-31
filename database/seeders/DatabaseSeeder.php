<?php
namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RoleSeeder::class,
        ]);

        // Create sample users
        \App\Models\User::factory(10)->create()->each(function ($user) {
            // Assign random role
            $roles = ['Admin', 'Project Manager', 'Developer'];
            $user->assignRole($roles[array_rand($roles)]);
        });
    }
}
