<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnReformTablePacs extends Migration
{
    /**
     * Bandera para saber si hay que hacer reforma cuando se sube el SRPAC reform=1 necesita reforma
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pacs', function (Blueprint $table) {
            $table->string('reform')->after('tipo_compra')->nullable()->default(\App\Pac::NO_REFORMAR_PAC);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pacs', function (Blueprint $table) {
            $table->dropColumn(['reform']);
        });
    }
}
