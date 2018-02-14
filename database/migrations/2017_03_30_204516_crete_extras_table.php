<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreteExtrasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('extras', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('area_item_id')->unsigned();
            $table->integer('item_id')->unsigned();
            $table->integer('area_id')->unsigned();
            $table->decimal('monto',10,2);
            $table->unsignedTinyInteger('mes');
            $table->timestamps();

            $table->foreign('area_item_id')->references('id')->on('area_item');
            $table->foreign('item_id')->references('id')->on('items');
            $table->foreign('area_id')->references('id')->on('areas');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('extras');
    }
}
