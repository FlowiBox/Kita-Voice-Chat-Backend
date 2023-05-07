<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MainPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table ('admin_permissions')->insert (
            [
                ['name'=>'All permission', 'slug'=>'*', 'http_method'=>'', 'http_path'=>'*',],
                ['name'=>'Dashboard', 'slug'=>'dashboard', 'http_method'=>'GET', 'http_path'=>'/',],
                ['name'=>'Login', 'slug'=>'auth.login', 'http_method'=>'', 'http_path'=>'/auth/login
/auth/logout',],
                ['name'=>'User setting', 'slug'=>'auth.setting', 'http_method'=>'GET,PUT', 'http_path'=>'/auth/setting',],
                ['name'=>'Auth management', 'slug'=>'auth.management', 'http_method'=>'', 'http_path'=>'/auth/roles
/auth/permissions
/auth/menu
/auth/logs',],
                ['name'=>'Admin helpers', 'slug'=>'ext.helpers', 'http_method'=>'', 'http_path'=>'/helpers/*',],
            ]
        );
    }
}
