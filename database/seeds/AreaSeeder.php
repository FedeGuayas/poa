<?php

use Illuminate\Database\Seeder;

class AreaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('areas')->insert([
            'area'=>'DTM',
            'cod_area'=>'DTM'
        ]);
        DB::table('areas')->insert([
            'area'=>'ADMINISTRATIVO',
            'cod_area'=>'ADM'
        ]);
        DB::table('areas')->insert([
            'area'=>'FINANCIERO',
            'cod_area'=>'FIN'
        ]);
        DB::table('areas')->insert([
            'area'=>'INFRAESTRUCTURA',
            'cod_area'=>'INFR'
        ]);
        DB::table('areas')->insert([
            'area'=>'MARKETING',
            'cod_area'=>'MARK'
        ]);
        DB::table('areas')->insert([
            'area'=>'TALENTO HUMANO',
            'cod_area'=>'TH'
        ]);
        DB::table('areas')->insert([
            'area'=>'PLANIFICACION',
            'cod_area'=>'PLAN'
        ]);
        DB::table('areas')->insert([
            'area'=>'JURÍDICO',
            'cod_area'=>'LEG'
        ]);
        DB::table('areas')->insert([
            'area'=>'GERENCIA',
            'cod_area'=>'GER'
        ]);
    }
}
