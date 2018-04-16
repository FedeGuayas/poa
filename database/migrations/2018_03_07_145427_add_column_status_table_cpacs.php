<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnStatusTableCpacs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cpacs', function (Blueprint $table) {
            $table->string('status')->after('certificado')->default(\App\Cpac::CPAC_INACTIVA);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cpacs', function (Blueprint $table) {
            $table->dropColumn(['status']);
        });
    }
}
