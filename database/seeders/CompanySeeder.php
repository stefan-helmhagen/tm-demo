<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('companies')->insert([
            'name'          => 'ACME',
            'location'      => 'Allentown',
            'created_at'    => now(),
            'updated_at'    => now()
        ]);
        DB::table('companies')->insert([
            'name'          => 'Contoso',
            'location'      => 'Charlotte',
            'created_at'    => now(),
            'updated_at'    => now()
        ]);
        DB::table('companies')->insert([
            'name'          => 'Oceanic Airlines',
            'location'      => 'Odessa',
            'created_at'    => now(),
            'updated_at'    => now()
        ]);
    }
}
