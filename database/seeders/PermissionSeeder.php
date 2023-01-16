<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PermissionSeeder extends Seeder
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

                //users
                ['name'=>'Browse Users','slug'=>'browse-users'],
                ['name'=>'Create Users','slug'=>'create-users'],
                ['name'=>'Edit Users','slug'=>'edit-users'],
                ['name'=>'show Users','slug'=>'show-users'],
                ['name'=>'delete Users','slug'=>'delete-users'],

                //usertarget
                ['name'=>'Browse UserTarget','slug'=>'browse-usertarget'],
                ['name'=>'Create UserTarget','slug'=>'create-usertarget'],
                ['name'=>'Edit UserTarget','slug'=>'edit-usertarget'],
                ['name'=>'show UserTarget','slug'=>'show-usertarget'],
                ['name'=>'delete UserTarget','slug'=>'delete-usertarget'],

                //rooms
                ['name'=>'Browse Rooms','slug'=>'browse-rooms'],
                ['name'=>'Create Rooms','slug'=>'create-rooms'],
                ['name'=>'Edit Rooms','slug'=>'edit-rooms'],
                ['name'=>'show Rooms','slug'=>'show-rooms'],
                ['name'=>'delete Rooms','slug'=>'delete-rooms'],

                //agencies
                ['name'=>'Browse Agencies','slug'=>'browse-agencies'],
                ['name'=>'Create Agencies','slug'=>'create-agencies'],
                ['name'=>'Edit Agencies','slug'=>'edit-agencies'],
                ['name'=>'show Agencies','slug'=>'show-agencies'],
                ['name'=>'delete Agencies','slug'=>'delete-agencies'],

                //backgrounds
                ['name'=>'Browse Backgrounds','slug'=>'browse-backgrounds'],
                ['name'=>'Create Backgrounds','slug'=>'create-backgrounds'],
                ['name'=>'Edit Backgrounds','slug'=>'edit-backgrounds'],
                ['name'=>'show Backgrounds','slug'=>'show-backgrounds'],
                ['name'=>'delete Backgrounds','slug'=>'delete-backgrounds'],

                //charge
                ['name'=>'Browse Charge','slug'=>'browse-charge'],
                ['name'=>'Create Charge','slug'=>'create-charge'],
                ['name'=>'Edit Charge','slug'=>'edit-charge'],
                ['name'=>'show Charge','slug'=>'show-charge'],
                ['name'=>'delete Charge','slug'=>'delete-charge'],

                //code
                ['name'=>'Browse Phone Code','slug'=>'browse-code'],
                ['name'=>'Create Phone Code','slug'=>'create-code'],
                ['name'=>'Edit Phone Code','slug'=>'edit-code'],
                ['name'=>'show Phone Code','slug'=>'show-code'],
                ['name'=>'delete Phone Code','slug'=>'delete-code'],

                //Config
                ['name'=>'Browse Config','slug'=>'browse-config'],
                ['name'=>'Create Config','slug'=>'create-config'],
                ['name'=>'Edit Config','slug'=>'edit-config'],
                ['name'=>'show Config','slug'=>'show-config'],
                ['name'=>'delete Config','slug'=>'delete-config'],

                //country
                ['name'=>'Browse Country','slug'=>'browse-country'],
                ['name'=>'Create Country','slug'=>'create-country'],
                ['name'=>'Edit Country','slug'=>'edit-country'],
                ['name'=>'show Country','slug'=>'show-country'],
                ['name'=>'delete Country','slug'=>'delete-country'],

                //cp
                ['name'=>'Browse CP','slug'=>'browse-cp'],
                ['name'=>'Create CP','slug'=>'create-cp'],
                ['name'=>'Edit CP','slug'=>'edit-cp'],
                ['name'=>'show CP','slug'=>'show-cp'],
                ['name'=>'delete CP','slug'=>'delete-cp'],

                //emoji
                ['name'=>'Browse Emoji','slug'=>'browse-emoji'],
                ['name'=>'Create Emoji','slug'=>'create-emoji'],
                ['name'=>'Edit Emoji','slug'=>'edit-emoji'],
                ['name'=>'show Emoji','slug'=>'show-emoji'],
                ['name'=>'delete Emoji','slug'=>'delete-emoji'],

                //family
                ['name'=>'Browse Family','slug'=>'browse-family'],
                ['name'=>'Create Family','slug'=>'create-family'],
                ['name'=>'Edit Family','slug'=>'edit-family'],
                ['name'=>'show Family','slug'=>'show-family'],
                ['name'=>'delete Family','slug'=>'delete-family'],

                //GIFT
                ['name'=>'Browse Gift','slug'=>'browse-gift'],
                ['name'=>'Create Gift','slug'=>'create-gift'],
                ['name'=>'Edit Gift','slug'=>'edit-gift'],
                ['name'=>'show Gift','slug'=>'show-gift'],
                ['name'=>'delete Gift','slug'=>'delete-gift'],

                //GIFT log
                ['name'=>'Browse GiftLog','slug'=>'browse-giftlog'],
                ['name'=>'Create GiftLog','slug'=>'create-giftlog'],
                ['name'=>'Edit GiftLog','slug'=>'edit-giftlog'],
                ['name'=>'show GiftLog','slug'=>'show-giftlog'],
                ['name'=>'delete GiftLog','slug'=>'delete-giftlog'],

                //Carousel
                ['name'=>'Browse Carousel','slug'=>'browse-carousel'],
                ['name'=>'Create Carousel','slug'=>'create-carousel'],
                ['name'=>'Edit Carousel','slug'=>'edit-carousel'],
                ['name'=>'show Carousel','slug'=>'show-carousel'],
                ['name'=>'delete Carousel','slug'=>'delete-carousel'],
            ]
        );
    }
}
