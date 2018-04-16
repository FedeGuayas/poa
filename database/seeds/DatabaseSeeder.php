<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call(UsersTableSeeder::class);
        $this->call(AreaSeeder::class);
        $this->call(DepartamentoSeeder::class);
        $this->call(RolesSeeder::class);
        $this->call(PermissionsSeeder::class);
        $this->call(MesesSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ReformTypeSeeder::class);
    }
}
