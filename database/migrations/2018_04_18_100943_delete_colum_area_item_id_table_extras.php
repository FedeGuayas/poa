<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DeleteColumAreaItemIdTableExtras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('extras', function (Blueprint $table) {
            $table->dropForeign(['area_item_id']);
            $table->dropColumn('area_item_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('extras', function (Blueprint $table) {
            $table->integer('area_item_id')->unsigned();
            $table->foreign('area_item_id')->references('id')->on('area_item');
        });
    }
}
