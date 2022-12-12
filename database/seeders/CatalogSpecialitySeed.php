<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSpecialitySeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('catalog_subjects')->insert([
            [
                'year' => 2022,
                'selective_discipline_id' => 2,
                'catalog_education_level_id' => 1,
                'speciality_id' => 580,
                'department_id' => 325,
                'faculty_id' => 414,
                'user_id' => 1,
            ],
            [
                'year' => 2022,
                'selective_discipline_id' => 2,
                'catalog_education_level_id' => 1,
                'speciality_id' => 763,
                'department_id' => 325,
                'faculty_id' => 414,
                'user_id' => 1,
            ],
            [
                'year' => 2022,
                'selective_discipline_id' => 2,
                'catalog_education_level_id' => 1,
                'speciality_id' => 1324,
                'department_id' => 325,
                'faculty_id' => 414,
                'user_id' => 1,
            ],
            [
                'year' => 2022,
                'selective_discipline_id' => 2,
                'catalog_education_level_id' => 1,
                'speciality_id' => 1443,
                'department_id' => 325,
                'faculty_id' => 414,
                'user_id' => 1,
            ],
        ]);
    }
}
