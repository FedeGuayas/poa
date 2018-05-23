<?php

/**
 *  Historico mensual del Esigef
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateEsigefsTable extends Migration
{
    /**
     * Historicos
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('esigefs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('mes');//cod del mes
            $table->char('ejercicio', 5);   //se elimino
            $table->char('cod_programa', 3); //programa en esigef
            $table->char('cod_actividad', 3); //actividad en esigef
            $table->char('cod_item', 7);//reglon en esigef
            $table->string('programa'); //eliminado
            $table->string('actividad'); //eliminado
            $table->string('item'); //eliminado
            $table->string('area',100); //eliminado
            $table->decimal('codificado',12,2);//codificado del esigef
            $table->decimal('devengado', 12,2);//devengado del esigef con los extras
            $table->decimal('planificado', 12,2);//eliminado
            $table->decimal('extras',10,2);//eliminado
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
        Schema::drop('esigefs');
    }
}
