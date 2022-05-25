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
            [
              'id' => 1,
              'title' => 'Молодший спеціаліст', 
              'created_at' => now()
            ],
            [
              'id' => 2,
              'title' => 'Бакалавр', 
              'created_at' => now()
            ],
            [
              'id' => 3,
              'title' => 'Спеціаліст', 
              'created_at' => now()
            ],
            [
              'id' => 4,
              'title' => 'Магістр', 
              'created_at' => now()
            ],
            [
              'id' => 8,
              'title' => 'Доктор філософії', 
              'created_at' => now()
            ]
        ]);
    }
}
