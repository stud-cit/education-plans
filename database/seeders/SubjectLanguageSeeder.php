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
                'title' => 'Українська',
                'created_at' => now()
            ],
            [
                'title' => 'English',
                'created_at' => now()
            ],

        ]);
    }
}
