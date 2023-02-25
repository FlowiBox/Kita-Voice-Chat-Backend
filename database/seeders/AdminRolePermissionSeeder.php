<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_role_permissions = array(
            array('role_id' => '1','permission_id' => '1','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','permission_id' => '1','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '3','permission_id' => '7','created_at' => NULL,'updated_at' => NULL)
        );
        DB::table ('admin_role_permissions')->insert ($admin_role_permissions);
    }
}
