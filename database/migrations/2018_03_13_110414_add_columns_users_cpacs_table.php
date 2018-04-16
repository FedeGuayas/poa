<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsUsersCpacsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cpacs', function (Blueprint $table) {
            $table->integer('user_sol_id')->unsigned()->after('status');//usuario que solicita
            $table->integer('user_aprueba_id')->unsigned()->nullable()->after('user_sol_id');//usuario que aprueba
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
            $table->dropColumn(['user_sol_id','user_aprueba_id']);
        });
    }
}
