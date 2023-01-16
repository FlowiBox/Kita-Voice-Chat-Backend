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
            ]
        );
    }
}
