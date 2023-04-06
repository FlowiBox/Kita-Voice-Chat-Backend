<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfigsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $configs = array(
            array('name' => 'min_tx_num','value' => '1000','desc' => NULL,'created_at' => '2022-11-26 23:57:24','updated_at' => '2022-11-26 23:57:24'),
            array('name' => 'storage_base_url','value' => 'http://localhost/yai-chat/public/storage','desc' => NULL,'created_at' => '2022-11-29 03:54:38','updated_at' => '2022-11-29 04:19:55'),
            array('name' => 'login_from_only_one_device','value' => 'yes','desc' => '','created_at' => '2022-12-18 15:54:52','updated_at' => '2022-12-18 15:57:13'),
            array('name' => 'platform_share','value' => '10','desc' => '','created_at' => '2022-12-18 15:55:07','updated_at' => '2022-12-18 15:57:38'),
            array('name' => 'cp_xssm','value' => '7','desc' => '','created_at' => '2022-12-18 15:55:23','updated_at' => '2022-12-18 15:57:56'),
            array('name' => 'one_usd_value_in_coins','value' => '10','desc' => '','created_at' => '2022-12-18 15:55:39','updated_at' => '2022-12-18 15:58:11'),
            array('name' => 'default_img','value' => '/images/1.png','desc' => 'الصورة الافتراضية','created_at' => '2023-02-25 12:49:09','updated_at' => '2023-02-25 12:49:09'),
            array('name' => 'twilio_sid','value' => '000','desc' => '','created_at' => '2023-02-25 12:49:09','updated_at' => '2023-02-25 12:49:09'),
            array('name' => 'twilio_api_key','value' => '000','desc' => '','created_at' => '2023-02-25 12:49:09','updated_at' => '2023-02-25 12:49:09'),
            array('name' => 'twilio_from','value' => '000','desc' => 'المرسل','created_at' => '2023-02-25 12:49:09','updated_at' => '2023-02-25 12:49:09'),
//            array('name' => 'f_yj_ratio','value' => '10','desc' => NULL,'created_at' => '2023-02-25 12:49:09','updated_at' => '2023-02-25 12:49:09'),
//            array('name' => 'union_share','value' => '10','desc' => NULL,'created_at' => '2023-02-25 12:49:09','updated_at' => '2023-02-25 12:49:09'),
//            array('name' => 'no_family_ratio','value' => '10','desc' => NULL,'created_at' => '2023-02-25 12:49:09','updated_at' => '2023-02-25 12:49:09'),
//            array('name' => '$is_family_ratio','value' => '20','desc' => NULL,'created_at' => '2023-02-25 12:49:09','updated_at' => '2023-02-25 12:49:09')
        );
        DB::table ('configs')->insert ($configs);
    }
}
