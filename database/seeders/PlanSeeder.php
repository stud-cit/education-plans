<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

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
            'form_study_id' => 1,
             'form_organization_id' => 1,
            'faculty_id' => 414,
            'department_id' => 325,
            'title' => 'Шаблон плану на 2022',
            'credits' => 30,
            'education_level_id' => 1,
            // 'verification_status_id' => 1,
            // 'term_study_id' => 1,
            'year' => 2022,
            'number_semesters' => 1,
            'speciality_id' => 1,
            'specialization' => 'Інформатика',
            'education_program_id' => 1,
            'qualification_id' => 1,
            'field_knowledge_id' => 1,
            'count_hours' => 1,
            'count_week' => 1,
            'created_at' => now()
        ]);
    }
}
