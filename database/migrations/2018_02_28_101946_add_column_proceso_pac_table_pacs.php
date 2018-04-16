<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnProcesoPacTablePacs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pacs', function (Blueprint $table) {
            $table->string('proceso_pac')->after('srpac')->default(\App\Pac::PROCESO_PAC);
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
            $table->dropColumn(['proceso_pac']);
        });
    }
}
