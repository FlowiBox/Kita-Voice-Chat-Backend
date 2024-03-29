<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('chat.table.messages_table'), function (Blueprint $table) {
            $table->increments('id');
            $table->integer('conversation_id');
            $table->string('conversation_type');
            $table->integer('user_id');
            $table->longText('text');
            $table->boolean('is_reading')->default(false);
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
        Schema::dropIfExists(config('chat.table.messages_table'));
    }
}