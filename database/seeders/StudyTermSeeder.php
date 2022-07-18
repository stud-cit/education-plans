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
                'semesters' => 8,
            ],
            [
                'title' => '1 рік 10 місяців',
                'year' => 1,
                'month' => 10,
                'course' => 2,
                'module' => 4,
                'semesters' => 4,
            ],
            [
                'title' => '1 рік 4 місяці',
                'year' => 1,
                'month' => 4,
                'course' => 2,
                'module' => 3,
                'semesters' => 3,
            ],
            [
                'title' => '3 роки 9 місяців',
                'year' => 3,
                'month' => 9,
                'course' => 4,
                'module' => 8,
                'semesters' => 8,
            ],
            [
                'title' => '1 рік 3 місяці',
                'year' => 1,
                'month' => 3,
                'course' => 2,
                'module' => 3,
                'semesters' => 3,
            ],
            [
                'title' => '1 рік 9 місяців',
                'year' => 1,
                'month' => 9,
                'course' => 2,
                'module' => 4,
                'semesters' => 4,
            ],
        ]);
    }
}
