<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnInclusionTablePacs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pacs', function (Blueprint $table) {
            $table->string('inclusion')->after('proceso_pac')->default(\App\Pac::PROCESO_INCLUSION_NO);
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
            $table->dropColumn(['inclusion']);
        });
    }
}
