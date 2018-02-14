<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('workers')->insert([
            'departamento_id'=>2,
            'nombres'=>'admin',
            'apellidos'=>'admin',
            'email'=>'admin@mail.com',
            'num_doc'=>'9999999999',
            'cargo'=>'ADMIN'
        ]);
        DB::table('users')->insert([
            'worker_id'=>1,
            'name'=>'admin',
            'email'=>'admin@mail.com',
            'password'=>bcrypt('123456'),
        ]);
        DB::table('role_user')->insert([
            'user_id'=>1,
            'role_id'=>2
        ]);
    }
}
