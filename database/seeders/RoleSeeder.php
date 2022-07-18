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
            ['title' => 'training-department', 'label' => 'Представник Навчального відділу'],
            ['title' => 'practice-department', 'label' => 'Представник Відділу практики'],
            ['title' => 'educational-department-deputy', 'label' => 'Заступник навчально-методичного відділу'],
            ['title' => 'educational-department-chief', 'label' => 'Начальник навчально-методичного відділу'],
            ['title' => 'faculty-institute', 'label' => 'Представники Факультету / Інституту'],
            ['title' => 'department', 'label' => 'Представники кафедр'],
            ['title' => 'root', 'label' => 'Розробник']
        ]);
    }
}
