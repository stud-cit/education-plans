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
            [
              'id' => 1,
              'title' => 'Модульно-циклова'
            ],
            [
              'id' => 3,
              'title' => 'Семестрова'
            ],
        ]);
    }
}
