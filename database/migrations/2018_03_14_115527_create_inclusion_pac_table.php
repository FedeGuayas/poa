<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateInclusionPacTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('inclusion_pac', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pac_id')->unsigned();
            $table->char('cod_item',6);
            $table->string('cpc')->nullable();
            $table->string('tipo_compra');
            $table->string('concepto');
            $table->decimal('presupuesto',10,2); //presupuesto que se desea agregar sin iva /1.12
            $table->string('inclusion_file')->nullable(); //archivo de la inclusion pac escaneado y aprobado
            $table->integer('user_sol_id')->unsigned();//usuario que solicita
            $table->integer('user_aprueba_id')->unsigned()->nullable();//usuario que aprueba
            $table->string('status')->default(\App\InclusionPac::INCLUSION_PAC_INACTIVA);
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
        Schema::drop('inclusion_pac');
    }
}
