<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCpresupuestariaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cpresupuestaria', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('pac_id')->unsigned();//proceso al que pertenece
            $table->string('cod_cert_presup')->nullable();//codigo cert presupuestaria
            $table->string('cert_presup')->nullable();//archivo escaneado de la cert presupuestaria
            $table->string('status')->default(\App\Cpresupuestaria::CPRES_INACTIVA);
            $table->integer('user_upload')->unsigned()->nullable();//id de usuario que sube la cert
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
        Schema::drop('cpresupuestaria');
    }
}
