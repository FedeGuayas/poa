<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePoaAnualsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('poa_anuals', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('exercise_id')->unsigned(); //ejercicio = año = 2017, 2018, etc
            $table->integer('month_id')->unsigned();//id del del mes del cierre, de la tabla months ...
            $table->char('cod_programa', 3); //cod_programa en items
            $table->char('cod_actividad', 3); //cod_actividad items
            $table->char('cod_item', 7); //cod_item en items
            $table->char('programa', 3); //programa en programas
            $table->char('actividad', 3); //actividad actividads
            $table->char('item', 7); //item(nombre) en items
            $table->decimal('presupuesto_plan', 12,2);//item preupuesto inicial planificado
            $table->decimal('presupuesto_real', 12,2)->nullable();//area_item->sum(monto), el presupuesto con sus reformas
            $table->decimal('devengado', 12,2);//devengado del esigef con los extras tomado de tabla de los cierres de esigefs
            $table->decimal('extras',10,2)->nullable();//ingresos extras del item sumado al area_item_monto=devengado_esigef
            $table->string('area',100);//area responsable, puede ser compartido area->area

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
        Schema::drop('poa_anuals');
    }
}
