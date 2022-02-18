<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormOrganizationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('form_organizations')->insert([
            ['title' => 'Модульно-циклова'],
            ['title' => 'Семестрова'],
        ]);
    }
}
