<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Run seeders in order (respecting foreign key constraints)
        $this->call([
            UserSeeder::class,          // Create users with different roles
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            // StockMovementSeeder can be run later if needed
        ]);
    }
}
