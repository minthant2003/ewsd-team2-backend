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

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        // Comment out this when using the DemoSeeder below
        // $this->call([
        //     RoleSeeder::class,
        //     DepartmentSeeder::class,
        //     UserSeeder::class,
        //     AcademicYearSeeder::class,
        //     CategorySeeder::class,
        // ]);

        // Uncomment this when using the DemoSeeder below
        $this->call([
            DemoSeeder::class,
        ]);
    }
}
