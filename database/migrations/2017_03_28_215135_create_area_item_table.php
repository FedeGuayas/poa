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
//            $table->enum('mes',['ENERO','FEBRERO','MARZO','ABRIL','MAYO','JUNIO','JULIO','AGOSTO','SEPTIEMBRE','OCTUBRE','NOVIEMBRE','DICIEMBRE']);
            $table->unsignedTinyInteger('mes');
            $table->timestamps();
            
            $table->foreign('item_id')->references('id')->on('items')
                ->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('area_id')->references('id')->on('areas');
            $table->unique(['item_id', 'mes']);
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
