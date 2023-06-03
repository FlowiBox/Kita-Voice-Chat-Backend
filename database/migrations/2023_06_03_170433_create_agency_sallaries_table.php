<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAgencySallariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('agency_sallaries', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('agency_id');
            $table->float('sallary');
            $table->float('cut_amount');
            $table->integer('month');
            $table->integer('year');
            $table->boolean('is_paid');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('agency_sallaries');
    }
}
