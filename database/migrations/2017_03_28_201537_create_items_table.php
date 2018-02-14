<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('actividad_programa_id')->unsigned();
            $table->char('cod_programa',6);
            $table->char('cod_actividad',6);
            $table->char('cod_item',6);
            $table->char('grupo_gasto',2);
            $table->string('item');
            $table->decimal('presupuesto',10,2);
            $table->decimal('disponible',10,2);
            $table->timestamps();

            $table->foreign('actividad_programa_id')->references('id')->on('actividad_programa')
                ->onUpdate('cascade')->onDelete('restrict');
            
            $table->unique(['actividad_programa_id','cod_item']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('items');
    }
}
