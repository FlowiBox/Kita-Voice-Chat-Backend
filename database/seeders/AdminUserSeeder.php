<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $admin_users = array(
            array('id' => '1','username' => 'developer','password' => '$2y$10$ty7vPnQw/wraKvwxbmXfpOqD33NZcDxhLRarSU7WiTCOL6VbJ1v/.','name' => 'Developer','avatar' => 'images/d3a9ec4148c1412cd8b501f1706c8c9f.jpg','remember_token' => 'vTvO2R4NX0aUH43Ji6iBxJYJ4Jh7WwijMkOHHF1C8w04adKAICLLkpRML8Su','created_at' => '2022-11-15 21:05:14','updated_at' => '2022-11-17 21:59:37','di' => '0'),
            array('id' => '2','username' => 'admin','password' => '$2y$10$Z4nSwpco8S6zP5qOeY1cxOezbWu2DNFx6x33zjYVc6SCKNggWBE.e','name' => 'Admin','avatar' => 'images/d3a9ec4148c1412cd8b501f1706c8c9f.jpg','remember_token' => 'ARfHr7Nr0ockALTmUlmO64m8ZxSVZslfD2andXYnOYq5fsxK3Flp6z7pdXGz','created_at' => '2022-11-17 21:47:27','updated_at' => '2022-11-17 21:57:36','di' => '0'),
            array('id' => '3','username' => 'agency','password' => '$2y$10$lby9mw0dIja8jxkGHflJturtDa7I90WThLiRkruh0vr431IrjqiVC','name' => 'Agency','avatar' => NULL,'remember_token' => NULL,'created_at' => '2023-02-25 16:48:38','updated_at' => '2023-02-25 16:48:38','di' => '0')
        );
        DB::table ('admin_users')->insert ($admin_users);
    }
}
