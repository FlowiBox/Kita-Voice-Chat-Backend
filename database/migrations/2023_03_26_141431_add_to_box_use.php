<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddToBoxUse extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('box_uses', function (Blueprint $table) {
            $table->integer ('used_coins')->nullable ()->default (0);
            $table->integer ('unused_coins')->nullable ()->default (0);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('box_uses', function (Blueprint $table) {
            //
        });
    }
}
