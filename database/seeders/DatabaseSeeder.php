<?php

namespace Database\Seeders;

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
        $this->call(MainPermissionSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(AdminUserSeeder::class);
        $this->call(AdminMemuSeeder::class);
        $this->call(AdminRoleMenuSeeder::class);
        $this->call(AdminRolePermissionSeeder::class);
        $this->call(AdminRoleSeeder::class);
        $this->call(AdminRoleUserSeeder::class);
        $this->call(ConfigsSeeder::class);
    }
}


