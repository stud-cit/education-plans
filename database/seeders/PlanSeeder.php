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
            // 'verification_status_id' => 1,
            'study_term_id' => 1,
            'year' => 2022,
            'number_semesters' => 1,
            'speciality_id' => 1,
            'specialization_id' => 370,
            'education_program_id' => 1,
            'qualification_id' => 1,
            'field_knowledge_id' => 1,
            'created_at' => now()
        ]);
    }
}
