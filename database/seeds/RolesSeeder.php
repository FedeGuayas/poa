<?php

use Illuminate\Database\Seeder;

class RolesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'name'=>'administrador',
            'display_name'=>'ADMINISTRADOR',
            'description'=>'ADMINISTRADOR DEL SISTEMA CON RESTRICCIONES',
        ]);
        DB::table('roles')->insert([
            'name'=>'root',
            'display_name'=>'SUPER ADMINISTRADOR',
            'description'=>'ADMINISTRADOR  SIN RESTRICCIONES',
        ]);
        DB::table('roles')->insert([
            'name'=>'responsable-poa',
            'display_name'=>'RESPONSABLE DEL POA',
            'description'=>'RESPONSABLE DEL POA DEL AREA',
        ]);
        DB::table('roles')->insert([
            'name'=>'responsable-pac',
            'display_name'=>'RESPONSABLE DE PROCESOS',
            'description'=>'TRABAJADOR RESPONSABLE DE PROCESOS DEL PAC',
        ]);
        DB::table('roles')->insert([
            'name'=>'analista',
            'display_name'=>'ANALISTA DE REFORMAS',
            'description'=>'REALIZA REFORMAS',
        ]);
        DB::table('roles')->insert([
            'name'=>'consultor',
            'display_name'=>'CONSULTOR',
            'description'=>'VER INFORMACION',
        ]);
        DB::table('roles')->insert([
            'name'=>'financiero',
            'display_name'=>'FINANCIERO',
            'description'=>'FINANCIERO',
        ]);
    }
}
