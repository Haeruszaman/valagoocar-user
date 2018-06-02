<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableAdmin extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admin', function (Blueprint $table) {
            $table->increments('id');
            $table->dateTime('register_time');
            $table->string('image', 100);
            $table->string('pin', 4);
            $table->string('name', 50);
            $table->string('username', 50)->unique();
            $table->string('city', 50);
            $table->string('address', 50);
            $table->string('email', 100);
            $table->string('password');
            $table->string('phone', 100);
            $table->string('roles', 50);
            $table->date('birthday');
            $table->string('gender', 10);
            $table->string('remember_token');
            $table->integer('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('admin');
    }
}
