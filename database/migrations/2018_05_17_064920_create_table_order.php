<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTableOrder extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('order', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code', 50)->unique();
            $table->string('service_code', 50);
            $table->string('user', 100);
            $table->date('order_date');
            $table->time('order_time');
            $table->integer('days');
            $table->date('end_date');
            $table->string('address_order', 50);
            $table->string('city', 50);
            $table->string('car', 50);
            $table->text('description');
            $table->double('price_total');
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
        Schema::dropIfExists('order');
    }
}
