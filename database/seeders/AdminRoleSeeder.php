<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_roles = array(
            array('id' => '1','name' => 'Developer','slug' => 'developer','created_at' => '2022-11-15 21:05:14','updated_at' => '2022-11-17 21:52:26'),
            array('id' => '2','name' => 'admin','slug' => 'admin','created_at' => '2022-11-17 21:35:30','updated_at' => '2022-11-17 21:51:35'),
            array('id' => '3','name' => 'Agency','slug' => 'agency','created_at' => '2023-01-09 18:18:28','updated_at' => '2023-01-09 18:18:28')
        );
        DB::table ('admin_roles')->insert ($admin_roles);
    }
}
