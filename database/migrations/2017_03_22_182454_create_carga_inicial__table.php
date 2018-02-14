<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCargaInicialTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('carga_inicial', function (Blueprint $table) {
            $table->increments('id');
            $table->char('ejercicio', 5);
            $table->char('cod_entidad', 3);
            $table->char('u_ejec', 2);
            $table->char('u_desc', 2);
            $table->char('programa', 3);
            $table->string('nombre_programa')->nullable();
            $table->char('sub_prog', 2);
            $table->char('proyecto', 2);
            $table->char('actividad', 3);
            $table->string('nombre_actividad')->nullable();
            $table->char('obra', 2);
            $table->char('renglon', 7);
            $table->string('nombre_item')->nullable();
            $table->char('geografico', 4);
            $table->char('fuente', 2);
            $table->char('organismo', 2);
            $table->char('correlativo', 2);
            $table->string('nomb_entidad', 100);
            $table->string('nomb_geo',100 );
            $table->string('asignado', 2);
            $table->decimal('codificado',12,2);
            $table->decimal('reserv_neg', 12,2);
            $table->decimal('precompromiso', 12,2);
            $table->decimal('compromiso', 12,2);
            $table->decimal('devengado', 12,2);
            $table->decimal('pagado', 12,2);
            $table->decimal('disponible', 12,2);
            $table->decimal('no_proyecto', 12,2);

//            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('carga_inicial');
    }
}
