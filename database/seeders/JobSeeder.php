<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class JobSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('jobs')->insert([
            'company_id'          => '1',
            'created_by_user_id'   => '1',
            'updated_by_user_id'   => '1',
            'title'                 => 'Web Developer',
            'description'           => 'We are looking for a frontend developer for our team. At ACME we work on our own projects and develop internal tools.',
            'location'              => 'Anytown',
            'contact_name'          => 'John Doe',
            'contact_email'         => 'john.doe@acme.com',
            'contact_phone'         => '555-1',
            'created_at'            => now(),
            'updated_at'            => now()
        ]);
        DB::table('jobs')->insert([
            'company_id'          => '2',
            'created_by_user_id'   => '2',
            'updated_by_user_id'   => '2',
            'title'                 => 'Content Manager',
            'description'           => 'We are looking for a content manager for our team. At Contoso we work on our own projects and develop internal tools.',
            'location'              => 'Redmond',
            'contact_name'          => 'John Random',
            'contact_email'         => 'john.random@contoso.com',
            'contact_phone'         => '555-2',
            'created_at'            => now(),
            'updated_at'            => now()
        ]);
        DB::table('jobs')->insert([
            'company_id'          => '3',
            'created_by_user_id'   => '3',
            'updated_by_user_id'   => '3',
            'title'                 => 'Database Administrator',
            'description'           => 'We are looking for a database administrator for our team. At Oceanic Airlines we work on our own projects and develop internal tools.',
            'location'              => 'Westpoint',
            'contact_name'          => 'John Smith',
            'contact_email'         => 'john.smith@oceanic.com',
            'contact_phone'         => '555-3',
            'created_at'            => now(),
            'updated_at'            => now()
        ]);
    }
}
