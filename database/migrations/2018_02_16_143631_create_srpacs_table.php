<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSrpacsTable extends Migration
{
    /** Tabla de solicitud de reforma pacs
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('srpacs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pac_id')->unsigned();
            $table->char('cod_item',6);
            $table->string('cpc')->nullable();
            $table->string('tipo_compra');
            $table->string('concepto');
            $table->decimal('presupuesto',10,2); //sin iva /1.12
            $table->string('solicitud_file')->nullable(); //archivo de la srpac escaneado
            $table->string('notas')->nullable();
            $table->integer('user_sol_id')->unsigned();//usuario que solicita
            $table->integer('user_aprueba_id')->unsigned()->nullable();//usuario que aprueba
            $table->string('status')->default(\App\Srpac::SRPAC_INACTIVA);

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
        Schema::drop('srpacs');
    }
}
