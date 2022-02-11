<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('form_studies')->insert([
            ['title' => 'денна', 'created_at' => now()],
            ['title' => 'заочна', 'created_at' => now()]
        ]);
    }
}
