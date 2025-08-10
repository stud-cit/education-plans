<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectHelperTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('catalog_helper_types')->insert([
            [
                'title' => 'Для дисцилін в навчальних планах',
                'key' => 'plan_subject'
            ],
        ]);
    }
}
