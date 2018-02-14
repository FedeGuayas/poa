<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePacsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pacs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('area_item_id')->unsigned();
            $table->integer('worker_id')->unsigned();
            $table->char('cod_item',6);
            $table->string('item');
            $table->string('concepto');
            $table->enum('procedimiento',['CATÁLOGO ELECTRÓNICO','CONTRATACIÓN DIRECTA','ÍNFIMA CUANTÍA','LICITACIÓN DE SEGUROS','SUBASTA INVERSA ELECTRÓNICA','OTRO']);
            $table->decimal('presupuesto',10,2);
            $table->decimal('comprometido',10,2)->nullable();//ejecutado
            $table->decimal('devengado',10,2)->nullable();//facturado y subido al esigef
            $table->decimal('disponible',10,2);//monto sin procesar
            $table->unsignedTinyInteger('mes');

            $table->timestamps();

            $table->foreign('area_item_id')->references('id')->on('area_item')
                ->onUpdate('cascade')->onDelete('restrict');
            $table->foreign('worker_id')->references('id')->on('workers')
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
        Schema::drop('pacs');
    }
}
