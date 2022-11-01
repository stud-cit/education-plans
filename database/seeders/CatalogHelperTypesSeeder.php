<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogHelperTypesSeeder extends Seeder
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
                'title' => 'Загальна компетентність, на формування або розвиток якої спрямована дисципліна',
                'key' => 'general_competence'
            ],
            [
                'title' => 'Результати навчання за навчальною дисципліною',
                'key' => 'learning_outcomes'
            ],
            [
                'title' => 'Види навчальних занять та методи викладання, що пропонуються',
                'key' => 'types_educational_activities'
            ],
            [
                'title' => 'Вхідні вимоги до здобувачів, які хочуть обрати дисципліну/вимоги до матеріально-технічного забезпечення',
                'key' => 'entry_requirements_applicants'
            ],
        ]);
    }
}
