<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnLiberadoTablePacs extends Migration
{
    /**
     *Columna que contiene el monto que puede ser tomado para la reforma
     *
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pacs', function (Blueprint $table) {
            $table->decimal('liberado',10,2)->after('disponible')->nullable()->default(0);
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
            $table->dropColumn(['liberado']);
        });
    }
}
