<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSelectiveSubjectSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('catalog_selective_subjects')->insert([
            'catalog_subject_id' => 1,
            'catalog_education_level_id' => 1,
            'user_id' => 1,
            'asu_id' => '00001',
            'title' => 'Вибори та виборчі системи',
            'list_fields_knowledge' => '1',
            'faculty_id' => 414,
            'department_id' => 325,
            'general_competence' => 1,
            'learning_outcomes' => 1,
            'types_educational_activities' => '1',
            'number_acquirers' => '1',
            'entry_requirements_applicants' => '1',
            'limitation' => '1'
        ]);
    }
}
