<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMaxAdminToRooms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('rooms', function (Blueprint $table) {
            $table->unsignedTinyInteger ('max_admin')->nullable ()->default (5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('rooms', function (Blueprint $table) {
            //
        });
    }
}
