<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReformasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('reformas', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('area_item_id')->unsigned();//poa origen al que se le realizara la reforma
            $table->integer('reform_type_id')->unsigned();//tipo reforma
            $table->integer('user_id')->unsigned();//usuario que solicita reforma
            $table->decimal('monto_orig',15,2);//valor total a disminuir de los diferentes pacs que componen este poa
            $table->string('estado')->default('Pendiente');//pendiente, aprobada, cancelada(eliminar)
            $table->string('nota')->nullable();
            $table->timestamps();

            $table->foreign('area_item_id')->references('id')->on('area_item')
                ->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('reformas');
    }
}
