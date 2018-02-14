<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActividadProgramaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actividad_programa', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('programa_id')->unsigned();
            $table->integer('actividad_id')->unsigned();
            $table->char('cod_actividad',3);
            $table->char('cod_programa',3);
           
            $table->foreign('actividad_id')->references('id')->on('actividads')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('programa_id')->references('id')->on('programas')
                ->onUpdate('cascade')->onDelete('cascade');

            $table->unique(['programa_id', 'actividad_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('actividad_programa');
    }
}
