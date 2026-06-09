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
        // Forzamos a Laravel a ignorar sus pruebas y ejecutar solo tu UserSeeder
        $this->call(UserSeeder::class);
    }
}
