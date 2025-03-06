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
        $this->call(\ModuleSystem\Seeders\DatabaseSeeder::class);
        $this->call(\ModuleReference\Seeders\DatabaseSeeder::class);
        $this->call(\ModuleFoundation\Seeders\DatabaseSeeder::class);
        $this->call(\ModuleMyFoundation\Seeders\DatabaseSeeder::class);
        $this->call(\ModuleTraining\Seeders\DatabaseSeeder::class);
        $this->call(\ModuleMyTraining\Seeders\DatabaseSeeder::class);
    }
}
