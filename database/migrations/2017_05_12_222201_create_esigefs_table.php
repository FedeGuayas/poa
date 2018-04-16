<?php

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
            $table->char('ejercicio', 5);
            $table->char('cod_programa', 3);
            $table->char('cod_actividad', 3);
            $table->char('cod_item', 7);
            $table->string('programa');
            $table->string('actividad');
            $table->string('item');
            $table->string('area',100);//area responsable 
            $table->decimal('codificado',12,2);//codificado del esigef
            $table->decimal('devengado', 12,2);//devengado del esigef con los extras
            $table->decimal('planificado', 12,2);//area_item_monto con sus reformas planificado_fdg
            $table->decimal('extras',10,2);//ingresos extras del item sumado al planificado_fdg=devengado_esigef
            $table->unsignedTinyInteger('mes');//mes del cierre
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
