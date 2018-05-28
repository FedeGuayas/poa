<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterReformasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('reformas', function (Blueprint $table) {
            $table->dropColumn(['tipo_informe', 'cod_informe', 'informe','num_min','num_modif']);
            $table->integer('informe_id')->unsigned()->after('nota')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('reformas', function (Blueprint $table) {
            $table->string('tipo_informe')->after('nota')->nullable();
            $table->string('cod_informe')->after('tipo_informe')->nullable();
            $table->string('informe')->after('cod_informe')->nullable();
            $table->integer('num_min')->after('informe')->nullable();
            $table->integer('num_modif')->after('num_min')->nullable();
            $table->dropColumn('informe_id');
        });
    }
}
