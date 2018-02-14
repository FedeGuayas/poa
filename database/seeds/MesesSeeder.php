<?php

use Illuminate\Database\Seeder;

class MesesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('months')->insert([
            'month'=>'ENERO',
            'cod'=>1
        ]);
        DB::table('months')->insert([
            'month'=>'FEBRERO',
            'cod'=>2
        ]);
        DB::table('months')->insert([
            'month'=>'MARZO',
            'cod'=>3
        ]);
        DB::table('months')->insert([
            'month'=>'ABRIL',
            'cod'=>4
        ]);
        DB::table('months')->insert([
            'month'=>'MAYO',
            'cod'=>5
        ]);
        DB::table('months')->insert([
            'month'=>'JUNIO',
            'cod'=>6
        ]);
        DB::table('months')->insert([
            'month'=>'JULIO',
            'cod'=>7
        ]);
        DB::table('months')->insert([
            'month'=>'AGOSTO',
            'cod'=>8
        ]);
        DB::table('months')->insert([
            'month'=>'SEPTIEMBRE',
            'cod'=>9
        ]);
        DB::table('months')->insert([
            'month'=>'OCTUBRE',
            'cod'=>10
        ]);
        DB::table('months')->insert([
            'month'=>'NOVIEMBRE',
            'cod'=>11
        ]);
        DB::table('months')->insert([
            'month'=>'DICIEMBRE',
            'cod'=>12
        ]);
    }
}
