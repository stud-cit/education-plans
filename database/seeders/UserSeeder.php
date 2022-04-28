<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('users')->insert([
            'asu_id' => '044fd9f4-83c3-e511-867d-001a4be6d04a',
            'faculty_id' => 414,
            'department_id' => 325,
            'offices_id' => 1,
            'role_id' => 1,
            'name' => Str::random(10),
            'email' => Str::random(10).'@gmail.com',
            // 'password' => Hash::make('password'),
        ]);
    }
}
