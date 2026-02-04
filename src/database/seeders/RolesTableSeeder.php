<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('roles')->insert([
            [
                'name' => 'superadmin',
                'guard_name' => 'web'
            ],
            [
                'name' => 'admin',
                'guard_name' => 'web'
            ],
            [
                'name' => 'manager',
                'guard_name' => 'web'
            ],

        ]);

        DB::table('permissions')->insert([
            //Permission Permission
            [
                'name' => 'create-permission',
                'guard_name' => 'web',
                'permission_group' => 'permission'
            ],

            [
                'name' => 'update-permission',
                'guard_name' => 'web',
                'permission_group' => 'permission'
            ],

            [
                'name' => 'delete-permission',
                'guard_name' => 'web',
                'permission_group' => 'permission'
            ],

            [
                'name' => 'view-permission',
                'guard_name' => 'web',
                'permission_group' => 'permission'
            ],

            //User Controller
            [
                'name' => 'create-user',
                'guard_name' => 'web',
                'permission_group' => 'user'
            ],

            [
                'name' => 'update-user',
                'guard_name' => 'web',
                'permission_group' => 'user'
            ],

            [
                'name' => 'delete-user',
                'guard_name' => 'web',
                'permission_group' => 'user'
            ],

            [
                'name' => 'view-user',
                'guard_name' => 'web',
                'permission_group' => 'user'
            ],
            //Organization Controller
            [
                'name' => 'create-organization',
                'guard_name' => 'web',
                'permission_group' => 'organization'
            ],

            [
                'name' => 'update-organization',
                'guard_name' => 'web',
                'permission_group' => 'organization'
            ],

            [
                'name' => 'delete-organization',
                'guard_name' => 'web',
                'permission_group' => 'organization'
            ],

            [
                'name' => 'view-organization',
                'guard_name' => 'web',
                'permission_group' => 'organization'
            ],
            
            // TransactionSource Controller
            [
                'name' => 'create-transaction-source',
                'guard_name' => 'web',
                'permission_group' => 'transaction-source'
            ],
            [
                'name' => 'update-transaction-source',
                'guard_name' => 'web',
                'permission_group' => 'transaction-source'
            ],

            [
                'name' => 'delete-transaction-source',
                'guard_name' => 'web',
                'permission_group' => 'transaction-source'
            ],

            [
                'name' => 'view-transaction-source',
                'guard_name' => 'web',
                'permission_group' => 'transaction-source'
            ],

            //Settings Controller
            [
                'name' => 'update-setting',
                'guard_name' => 'web',
                'permission_group' => 'setting'
            ],

        ]);

        Role::find(2)
            ->givePermissionTo(Permission::all())
            ->revokePermissionTo([
                'create-permission',
                'update-permission',
                'delete-permission',
                'view-permission',
            ]);
    }
}
