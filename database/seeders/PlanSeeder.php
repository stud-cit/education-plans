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
            // 'form_studies_id' => '',
            'faculty_id' => 1,
            'department_id' => 2,
            'title' => 'Шаблон плану на 2022',
            'credits' => 30,
            // 'education_level_id' => 13,
            // 'verification_status_id' => 1,
            // 'term_study_id' => 1,
            'year' => 1,
            'number_semesters' => 1,
            'specialization_id' => 1,
            'specialization' => 'Інформатика',
            'education_program_id' => 1,
            'qualification_id' => 1,
            'field_knowledge_is' => 1,
            // 'form_organization_id' => 1,
            'count_hours' => 1,
            'count_week' => 1,
            'created_at' => now()
        ]);
    }
}