<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePacOrigenTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pac_origen', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('reforma_id')->unsigned();
            $table->integer('pac_id')->unsigned();
            $table->decimal('valor_orig',15,2);//sub total que component el monto_orig de la reforma
            $table->string('estado',15)->default('Pendiente');
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
        Schema::drop('pac_origen');
    }
}
