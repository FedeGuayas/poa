<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAreaItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('area_item', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->integer('area_id')->unsigned();
            $table->decimal('monto',10,2);
            $table->unsignedTinyInteger('mes');
            $table->timestamps();
            
            $table->foreign('item_id')->references('id')->on('items')
                ->onUpdate('cascade')->onDelete('restrict');
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
        Schema::drop('area_item');
    }
}
