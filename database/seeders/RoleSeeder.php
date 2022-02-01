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
        $roles = [
            ['admin', 'Адміністратор'],
            ['educational-department','Представник Навчально-методичного відділу'],
            ['practice-department','Представник Відділу практики'],
            ['training-department;', 'Представник Навчального відділу'],
            ['faculty-institute', 'Представники Факультету / Інституту'],
            ['department', 'Представники кафедр']
        ];

        foreach ($roles as $role) {
            DB::table('roles')->insert([
                'title' => $role[0],
                'label' => $role[1]
            ]);
        }

    }
}
