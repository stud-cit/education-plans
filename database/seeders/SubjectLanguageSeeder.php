<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectLanguageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subject_languages')->insert([
            [
                'title' => 'English',
                'created_at' => now()
            ],
            [
                'title' => 'Українська',
                'created_at' => now()
            ]
        ]);
    }
}
