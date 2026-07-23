<?php

declare(strict_types=1);

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * Seeder principale del database.
 * Crea i dati iniziali e chiama gli altri seeder
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Popola il database dell'applicazione.
     */
    public function run(): void
    {
        $this->call([
            DemoDataSeeder::class,
        ]);
    }
}
