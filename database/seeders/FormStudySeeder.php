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
            ['title' => 'Денна', 'created_at' => now()],
            ['title' => 'Заочна', 'created_at' => now()]
        ]);
    }
}
