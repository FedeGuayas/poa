<?php

use Illuminate\Database\Seeder;

class DepartamentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('departamentos')->insert([
            'area_id'=>1,
            'departamento'=>'DIRECCIÓN',
        ]);
        DB::table('departamentos')->insert([
            'area_id'=>2,
            'departamento'=>'DIRECCIÓN',
        ]);
        DB::table('departamentos')->insert([
            'area_id'=>3,
            'departamento'=>'DIRECCIÓN',
        ]);
        DB::table('departamentos')->insert([
            'area_id'=>4,
            'departamento'=>'DIRECCIÓN',
        ]);
        DB::table('departamentos')->insert([
            'area_id'=>5,
            'departamento'=>'DIRECCIÓN',
        ]);
        DB::table('departamentos')->insert([
            'area_id'=>6,
            'departamento'=>'DIRECCIÓN',
        ]);
        DB::table('departamentos')->insert([
            'area_id'=>7,
            'departamento'=>'DIRECCIÓN',
        ]);

    }
}
