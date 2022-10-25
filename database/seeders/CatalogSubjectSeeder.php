<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('catalog_subjects')->insert([
            [
                'year' => 2022,
                'selective_discipline_id' => 1,
                'group_id' => 1,
                'user_id' => 1,
            ],
            [
                'year' => 2022,
                'selective_discipline_id' => 1,
                'group_id' => 2,
                'user_id' => 1,
            ],
            [
                'year' => 2022,
                'selective_discipline_id' => 1,
                'group_id' => 3,
                'user_id' => 1,
            ],
            [
                'year' => 2022,
                'selective_discipline_id' => 1,
                'group_id' => 4,
                'user_id' => 1,
            ],
            [
                'year' => 2022,
                'selective_discipline_id' => 1,
                'group_id' => 5,
                'user_id' => 1,
            ],
            [
                'year' => 2022,
                'selective_discipline_id' => 1,
                'group_id' => 6,
                'user_id' => 1,
            ],
            [
                'year' => 2022,
                'selective_discipline_id' => 1,
                'group_id' => 7,
                'user_id' => 1,
            ],
        ]);
    }
}
