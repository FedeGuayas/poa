<?php

/**
 *  Historico anual del Esigef
 */

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterEsigefsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('esigefs', function (Blueprint $table) {

            $table->dropColumn(['ejercicio', 'programa', 'actividad','item','area','planificado','extras']);

            $table->integer('exercise_id')->unsigned()->after('id'); //ejercicio = año = 2017, 2018, etc

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('esigefs', function (Blueprint $table) {
            $table->char('ejercicio', 5);   //se elimino
            $table->string('programa'); //eliminado
            $table->string('actividad'); //eliminado
            $table->string('item'); //eliminado
            $table->string('area',100); //eliminado
            $table->decimal('planificado', 12,2);//eliminado
            $table->decimal('extras',10,2);//eliminado

            $table->dropColumn('exercise_id');

        });
    }
}
