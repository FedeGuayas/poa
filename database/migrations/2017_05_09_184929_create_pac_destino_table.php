<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePacDestinoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pac_destino', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reforma_id')->unsigned();
            $table->integer('pac_id')->unsigned();//pac destino
            $table->decimal('valor_dest',15,2);// sub total que component el monto_destino de la reforma
            
            $table->timestamps();

            $table->foreign('reforma_id')->references('id')->on('reformas')
                ->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('pac_id')->references('id')->on('pacs');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('pac_destino');
    }
}
