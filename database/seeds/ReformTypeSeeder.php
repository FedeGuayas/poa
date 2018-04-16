<?php

use Illuminate\Database\Seeder;

class ReformTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reform_type')->insert([
            'tipo_reforma'=>'INTERNA'
        ]);
        DB::table('reform_type')->insert([
            'tipo_reforma'=>'INFORMATIVA'
        ]);
        DB::table('reform_type')->insert([
            'tipo_reforma'=>'MINISTERIAL'
        ]);
    }
}
