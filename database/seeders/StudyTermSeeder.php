<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudyTermSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('study_terms')->Insert([
            [
                'title' => '3 роки 10 місяців',
                'year' => 3,
                'month' => 10,
                'course' => 4,
                'module' => 8,
                'number_semesters' => 8,
            ],
            [
                'title' => '1 роки 10 місяців',
                'year' => 1,
                'month' => 10,
                'course' => 2,
                'module' => 8,
                'number_semesters' => 4,
            ]
            ]
        );
    }
}
