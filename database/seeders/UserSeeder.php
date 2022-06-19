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
        [
          'asu_id' => '044fd9f4-83c3-e511-867d-001a4be6d04a',
          'name' => 'Adam',
          'faculty_id' => 414,
          'faculty_name' => 'Elit',
          'department_id' => 325,
          'department_name' => 'Komp nauk',
          'role_id' => 1,
          'email' => 'admin@gmail.com',
          'password' => Hash::make('password')
        ],
        [
          'asu_id' => '22e6106c-c580-e711-8194-001a4be6d04a',
          'name' => 'User 2',
          'faculty_id' => 414,
          'faculty_name' => 'Elit',
          'department_id' => 325,
          'department_name' => 'Komp nauk',
          'role_id' => 2,
          'email' => 'training@gmail.com',
          'password' => Hash::make('password')
        ],
        [
          'asu_id' => '22e6106c-c580-e711-8194-001a4be6d05a',
          'name' => 'User 3',
          'faculty_id' => 414,
          'faculty_name' => 'Elit',
          'department_id' => 325,
          'department_name' => 'Komp nauk',
          'role_id' => 3,
          'email' => 'practice@gmail.com',
          'password' => Hash::make('password')
        ],
        [
          'asu_id' => '22e6106c-c580-e711-8194-001a4be6d06a',
          'name' => 'User 4',
          'faculty_id' => 414,
          'faculty_name' => 'Elit',
          'department_id' => 325,
          'department_name' => 'Komp nauk',
          'role_id' => 4,
          'email' => 'educational@gmail.com',
          'password' => Hash::make('password')
        ],
        [
          'asu_id' => '22e6106c-c580-e711-8194-001a4be6d07a',
          'name' => 'User 5',
          'faculty_id' => 414,
          'faculty_name' => 'Elit',
          'department_id' => 325,
          'department_name' => 'Komp nauk',
          'role_id' => 5,
          'email' => 'faculty@gmail.com',
          'password' => Hash::make('password')
        ],
        [
          'asu_id' => '22e6106c-c580-e711-8194-001a4be6d08a',
          'name' => 'User 6',
          'faculty_id' => 414,
          'faculty_name' => 'Elit',
          'department_id' => 325,
          'department_name' => 'Komp nauk',
          'role_id' => 6,
          'email' => 'department@gmail.com',
          'password' => Hash::make('password')
        ]
      ]);
    }
}
