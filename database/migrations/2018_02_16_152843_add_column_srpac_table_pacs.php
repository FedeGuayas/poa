<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnSrpacTablePacs extends Migration
{
    /**
     * bandera srpac=0 no actualizar pac automaticamente, srpac=1 actua;iza automaticamente el pac
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pacs', function (Blueprint $table) {
            $table->string('srpac')->after('reform')->nullable()->default(\App\Pac::NO_APROBADA_SRPAC);
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
            $table->dropColumn(['srpac']);
        });
    }
}
