<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVinculadosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vinculados', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('item_id')->unsigned();
            $table->char('codigo_item',6);
            $table->string('ruc');
            $table->string('nombre_ruc');
            $table->decimal('inicial',10,2);
            $table->decimal('codificado',10,2);
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('vinculados');
    }
}
