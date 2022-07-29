<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plans')->insert([
            'guid' => Str::uuid(),
            'form_study_id' => 1,
            'form_organization_id' => 3,
            'faculty_id' => 414,
            'department_id' => 325,
            'title' => 'Шаблон плану на 2022',
            'credits' => 30,
            'education_level_id' => 1,
            'study_term_id' => 1,
            'year' => 2022,
            'number_semesters' => 1,
            'speciality_id' => null,
            'specialization_id' => null,
            'education_program_id' => null,
            'qualification_id' => 64,
            'field_knowledge_id' => 122,
            'hours_weeks_semesters' => json_encode([
                ["hour" => 0, "week" => 3, "index" => 1, "course" => 1, "semester" => 1],
                ["hour" => 0, "week" => 3, "index" => 1, "course" => 1, "semester" => 2],
                ["hour" => 0, "week" => 3, "index" => 1, "course" => 2, "semester" => 3],
                ["hour" => 0, "week" => 3, "index" => 1, "course" => 2, "semester" => 4],
                ["hour" => 0, "week" => 3, "index" => 1, "course" => 3, "semester" => 5],
                ["hour" => 0, "week" => 3, "index" => 1, "course" => 3, "semester" => 6],
                ["hour" => 0, "week" => 3, "index" => 1, "course" => 4, "semester" => 7],
                ["hour" => 0, "week" => 3, "index" => 1, "course" => 4, "semester" => 8]
            ]),
            'author_id' => 1,
            'published' => 1,
            'created_at' => now()
        ]);
    }
}
