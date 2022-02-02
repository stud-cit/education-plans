<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            ['title' => 'admin', 'label' => 'Адміністратор'],
            ['title' => 'educational-department', 'label' => 'Представник Навчально-методичного відділу'],
            ['title' => 'practice-department', 'label' => 'Представник Відділу практики'],
            ['title' => 'training-department', 'label' => 'Представник Навчального відділу'],
            ['title' => 'faculty-institute', 'label' => 'Представники Факультету / Інституту'],
            ['title' => 'department', 'label' => 'Представники кафедр']
        ]);
    }
}
