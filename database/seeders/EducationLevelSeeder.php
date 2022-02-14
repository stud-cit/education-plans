<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('education_levels')->insert([
            ['title' => 'Бакалавр', 'created_at' => now()],
            ['title' => 'Магістр', 'created_at' => now()],
            ['title' => 'Доктор філософії', 'created_at' => now()]
        ]);
    }
}
