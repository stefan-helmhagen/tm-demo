<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(JobSeeder::class);
        $this->call(CompanySeeder::class);
        $this->call(CompanyUserSeeder::class);
        \App\Models\User::factory(3)->create();
    }
}
