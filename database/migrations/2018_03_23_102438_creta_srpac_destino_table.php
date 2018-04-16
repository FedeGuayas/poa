<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CretaSrpacDestinoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('srpac_destino', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('srpac_id')->unsigned();
            $table->integer('pac_id')->unsigned(); //pac destino
            $table->char('cod_item',6);
            $table->string('cpc')->nullable();
            $table->string('tipo_compra');
            $table->string('concepto');
            $table->decimal('presupuesto',10,2); //sin iva  /1.12

            $table->timestamps();

            $table->foreign('srpac_id')->references('id')->on('srpacs')
                ->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('srpac_destino');
    }
}
