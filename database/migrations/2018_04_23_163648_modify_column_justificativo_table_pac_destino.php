<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ModifyColumnJustificativoTablePacDestino extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('pac_destino', function (Blueprint $table) {
            $table->text('justificativo')->nullable()->change();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('pac_destino', function (Blueprint $table) {
            $table->string('justificativo')->nullable()->after('valor_dest')->change();
        });
    }
}
