<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRoleUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_role_users = array(
            array('role_id' => '1','user_id' => '1','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','user_id' => '2','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '3','user_id' => '3','created_at' => NULL,'updated_at' => NULL)
        );
        DB::table ('admin_role_users')->insert ($admin_role_users);
    }
}
