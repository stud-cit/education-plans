<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LanguageSubjectSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('language_subjects')->insert([
            [
                'language_id' => 1,
                'subject_id' => 1,
            ],
            [
                'language_id' => 2,
                'subject_id' => 1,
            ],
        ]);
    }
}
