<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TermStudySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('term_studies')->insert([
            'title' => '3 роки 10 місяців',
            'year' => 3,
            'month' => 10,
            'course' => 4,
            'module' => 8
        ]);
    }
}
