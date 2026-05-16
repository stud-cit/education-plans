<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdminDepartmentPostgraduateRoleSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('roles')->insert([
            'title' => 'admin_department_postgraduate',
            'label' => 'Адміністратор підрозділу (ННЦ ПКВК)'
        ]);
    }
}
