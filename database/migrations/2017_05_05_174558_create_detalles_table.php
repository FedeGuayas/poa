<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDetallesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('detalles', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pac_id')->unsigned();
            $table->string('proveedor');
            $table->string('num_doc');
            $table->string('num_factura');
            $table->date('fecha_factura');
            $table->date('fecha_entrega')->nullable();
            $table->decimal('importe',10,2);
            $table->string('nota')->nullable();
            $table->string('estado',15)->default('Pendiente');
            $table->timestamps();

            $table->foreign('pac_id')->references('id')->on('pacs')
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
        Schema::drop('detalles');
    }
}
