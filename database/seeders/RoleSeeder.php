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
            ['id' => 1, 'title' => 'admin', 'label' => 'Адміністратор'],
            ['id' => 2, 'title' => 'training-department', 'label' => 'Представник Навчального відділу'],
            ['id' => 3, 'title' => 'practice-department', 'label' => 'Представник Відділу практики'],
            ['id' => 4, 'title' => 'educational-department-deputy', 'label' => 'Заступник навчально-методичного відділу'],
            ['id' => 5, 'title' => 'educational-department-chief', 'label' => 'Начальник навчально-методичного відділу'],
            ['id' => 6, 'title' => 'faculty-institute', 'label' => 'Представники Факультету / Інституту'],
            ['id' => 7, 'title' => 'department', 'label' => 'Представники кафедр'],
            ['id' => 8, 'title' => 'root', 'label' => 'Розробник'],
            ['id' => 9, 'title' => 'guest', 'label' => 'Гість'],
            ['id' => 10, 'title' => 'admin_department_postgraduate', 'label' => 'Адміністратор підрозділу (ННЦ ПКВК)'],
        ]);
    }
}
