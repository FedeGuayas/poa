<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpacsTable extends Migration
{
    /**
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
            $table->decimal('monto',10,2);
            $table->string('certificado')->nullable();
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
