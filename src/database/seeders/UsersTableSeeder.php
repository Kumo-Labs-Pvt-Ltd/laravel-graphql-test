<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Super Admin',
                'email' => 'superadmin@ledgerapp.com',
                'password' => Hash::make('password'),
                'organization_id' => null,
                'is_active'=>true,
            ],
            [
                'name' => 'Admin',
                'email' => 'admin@ledgerapp.com',
                'password' => Hash::make('password'),
                'organization_id' => null,
                'is_active'=>true,
            ],
            [
                'name' => 'Employee',
                'email' => 'employee@ledgerapp.com',
                'password' => Hash::make('password'),
                'organization_id' => null,
                'is_active'=>true,
            ],
            [
                'name' => 'Sushant KC',
                'email' => 'sushant@eskecy.com',
                'password' => Hash::make('password'),
                'organization_id' => 1,
                'is_active'=>true,
            ],
            [
                'name' => 'Ishan Kc',
                'email' => 'ishan@eskecy.com',
                'password' => Hash::make('password'),
                'organization_id' => 1,
                'is_active'=>true,
            ],
            [
                'name' => 'Suresh Karn',
                'email' => 'suresh@chhoriwears.com',
                'password' => Hash::make('password'),
                'organization_id' => 2,
                'is_active'=>true,
            ],

        ]);

        User::find(1)->assignRole('superadmin');
        User::find(2)->assignRole('admin');
        User::find(3)->assignRole('manager');

        User::find(4)->assignRole('admin');
        User::find(5)->assignRole('manager');
        User::find(6)->assignRole('admin');
        User::find(7)->assignRole('manager');
    }
}
