<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganizationsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('organizations')->insert([
            [
                'name' => 'Eskecy Pvt Ltd',
                'slug' => 'eskecy-pvt-ltd',
                'joined_date' => now(),
                'expiry_date' => now()->addYear(),
                'is_active' => true,
            ],
            [
                'name' => 'Chhori Wears',
                'slug' => 'chhori-wears',
                'joined_date' => now(),
                'expiry_date' => now()->addYear(),
                'is_active' => true,
            ]
        ]);
    }
}
