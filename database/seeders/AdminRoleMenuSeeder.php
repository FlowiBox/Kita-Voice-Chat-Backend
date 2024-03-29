<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRoleMenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_role_menu = array(
            array('role_id' => '1','menu_id' => '2','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '8','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '2','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '6','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '7','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '13','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '13','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '14','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '14','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '15','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '16','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '16','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '17','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '17','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '18','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '18','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '19','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '19','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '20','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '20','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '21','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '21','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '22','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '22','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '24','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '24','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '25','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '25','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '26','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '26','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '27','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '27','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '28','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '28','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '29','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '29','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '30','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '30','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '31','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '31','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '32','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '32','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '33','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '33','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '34','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '34','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '35','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '35','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '36','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '36','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '3','menu_id' => '37','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '1','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '1','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '3','menu_id' => '35','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '3','menu_id' => '38','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '3','menu_id' => '40','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '41','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '41','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '42','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '42','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '43','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '43','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '44','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '44','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '45','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '45','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '46','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '46','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '47','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '47','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '48','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '48','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '1','menu_id' => '49','created_at' => NULL,'updated_at' => NULL),
            array('role_id' => '2','menu_id' => '49','created_at' => NULL,'updated_at' => NULL)
        );
        DB::table ('admin_role_menu')->insert ($admin_role_menu);
    }
}
