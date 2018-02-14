<?php

use Illuminate\Database\Seeder;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('permissions')->insert([
            'name'=>'ver-esigef',
            'display_name'=>'VER ESIGEF',
            'description'=>'VER LA INFORMACION DEL ESIGEF',
        ]);
        DB::table('permissions')->insert([
            'name'=>'admin-roles',
            'display_name'=>'ADMINISTRA ROLES',
            'description'=>'CREAR, ELIMINAR, ACTUALIZAR ROLES',
        ]);
        DB::table('permissions')->insert([
            'name'=>'admin-permisos',
            'display_name'=>'ADMINISTRA PERMISOS',
            'description'=>'CREAR, ELIMINAR, ACTUALIZAR PERMISOS O ABILIDADES',
        ]);
        DB::table('permissions')->insert([
            'name'=>'gestiona-roles',
            'display_name'=>'GESTIONA LOS ROLES DE USUARIOS',
            'description'=>'PUEDE ASIGNAR Y QUITAR ROLES A LOS USUARIOS',
        ]);
        DB::table('permissions')->insert([
            'name'=>'gestiona-permisos',
            'display_name'=>'GESTIONA LOS PERMISOS DE USUARIOS',
            'description'=>'PUEDE ASIGNAR Y QUITAR PERMISOS A LOS USUARIOS',
        ]);
        DB::table('permissions')->insert([
            'name'=>'importa-esigef',
            'display_name'=>'IMPORTA ESIGEF',
            'description'=>'IMPORTA BASES DIARIAS DEL ESIGEF',
        ]);
        DB::table('permissions')->insert([
            'name'=>'importa-presupuesto',
            'display_name'=>'IMPORTA PRESUPUESTO',
            'description'=>'IMPORTA PRESUPUESTO ANUAL',
        ]);
        DB::table('permissions')->insert([
            'name'=>'admin-direcciones', //AREAS
            'display_name'=>'ADMINISTRA LAS DIRECCIONES',
            'description'=>'CREA, ELIMINA, ACTUALIZA LAS DIRECCIONES',
        ]);
        DB::table('permissions')->insert([
            'name'=>'admin-coordinaciones',//DEPARTAMENTOS
            'display_name'=>'ADMINISTRA LAS COORDINACIONES',
            'description'=>'CREA, ELIMINA, ACTUALIZA LAS COORDINACIONES',
        ]);
        DB::table('permissions')->insert([
            'name'=>'admin-programas',
            'display_name'=>'ADMINISTRA LOS PROGRAMAS',
            'description'=>'CREA, ELIMINA, ACTUALIZA LOS PROGRAMAS',
        ]);
        DB::table('permissions')->insert([
            'name'=>'admin-actividades',
            'display_name'=>'ADMINISTRA LAS ACTIVIDADES',
            'description'=>'CREA, ELIMINA, ACTUALIZA LAS ACTIVIDADES',
        ]);
        DB::table('permissions')->insert([
            'name'=>'admin-items',
            'display_name'=>'ADMINISTRA LOS ITEMS',
            'description'=>'CREA, ELIMINA, ACTUALIZA LOS ITEMS',
        ]);
        DB::table('permissions')->insert([
            'name'=>'admin-trabajadores',
            'display_name'=>'ADMINISTRA LOS TRABAJADORES',
            'description'=>'CREA, ELIMINA, ACTUALIZA A LOS TRABAJADORES O USUARIOS',
        ]);
        DB::table('permissions')->insert([
            'name'=>'gestion-procesos',//PAC
            'display_name'=>'GESTIONAR PROCESOS',
            'description'=>'GESTIONA SUS PROPIOS PROCESOS',
        ]);
        DB::table('permissions')->insert([
            'name'=>'admin-reformas',
            'display_name'=>'ADMINISTRA REFORMAS',
            'description'=>'CREA, ELIMINA, APRUEBA LAS REFORMAS',
        ]);
        DB::table('permissions')->insert([
            'name'=>'solicita-reformas',
            'display_name'=>'SOLICITA REFORMAS',
            'description'=>'CREA LAS REFORMAS',
        ]);
        DB::table('permissions')->insert([
            'name'=>'imprimir-reformas',
            'display_name'=>'IMPRIME REFORMAS',
            'description'=>'LISTA E IMPRIME LAS REFORMAS',
        ]);
        DB::table('permissions')->insert([
            'name'=>'planifica-poa',
            'display_name'=>'PLANIFICA POA',
            'description'=>'CREA, PLANIFICA, ACTUALIZA POA DE LAS AREAS Y LOS INGRESOS EXTRAS',
        ]);
        DB::table('permissions')->insert([
            'name'=>'planifica-pac',
            'display_name'=>'PLANIFICA PAC',
            'description'=>'CREA, PLANIFICA, ACTUALIZA PAC SU AREA ',
        ]);
        DB::table('permissions')->insert([
            'name'=>'aprueba-devengado',
            'display_name'=>'APRUEBA DEVENGADO',
            'description'=>'APRUEBA EL MONTO DEVENGADO EN UN PROCESO',
        ]);
        DB::table('permissions')->insert([
            'name'=>'consultor',
            'display_name'=>'CONSULTOR',
            'description'=>'ACCESO DE SOLO LECTURA',
        ]);
        DB::table('permissions')->insert([
            'name'=>'ver-historico',
            'display_name'=>'VER EL HISTORICO',
            'description'=>'USUARIOS QUE PUEDEN VER EL HISTORICO',
        ]);
        DB::table('permissions')->insert([
            'name'=>'hacer-cierre',
            'display_name'=>'CIERRE MENSUAL',
            'description'=>'REALIZA EL CIERRE MENSUAL PARA HISTORICO',
        ]);

    }
}
