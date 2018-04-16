<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpacsTable extends Migration
{
    /**
     *  Tabla donde se guarda la certificacion pac
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpacs', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pac_id')->unsigned();
            $table->string('partida');//item
            $table->string('cpc')->nullable();
            $table->decimal('monto',10,2); //Sin iva monto/1.12
            $table->string('certificado')->nullable(); //archivo escaneado de la cpac
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
        Schema::drop('cpacs');
    }
}
