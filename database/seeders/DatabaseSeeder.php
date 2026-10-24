<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * Administrator accounts must be provisioned explicitly with the
     * admin:create command. No shared demo credentials are seeded.
     */
    public function run(): void
    {
        $this->call(ServiceLineSeeder::class);
    }
}
