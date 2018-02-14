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
            'area'=>'DTM'
        ]);
        DB::table('areas')->insert([
            'area'=>'ADMINISTRATIVO'
        ]);
        DB::table('areas')->insert([
            'area'=>'FINANCIERO'
        ]);
        DB::table('areas')->insert([
            'area'=>'INFRAESTRUCTURA'
        ]);
        DB::table('areas')->insert([
            'area'=>'MARKETING'
        ]);
        DB::table('areas')->insert([
            'area'=>'TH'
        ]);
        DB::table('areas')->insert([
            'area'=>'PLANIFICACION'
        ]);
    }
}
