<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyAreaItemTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('area_item', function (Blueprint $table) {
            $table->decimal('monto',10,2)->nullable()->default(0)->change();
            $table->string('inclusion')->after('mes')->default(\App\AreaItem::INCLUSION_NO);
            $table->unique(['item_id', 'area_id','mes']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('area_item', function (Blueprint $table) {
            $table->dropColumn(['inclusion']);
        });
    }
}
