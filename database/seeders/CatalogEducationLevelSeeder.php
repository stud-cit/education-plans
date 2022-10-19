<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogEducationLevelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('catalog_education_levels')->insert([
            [
                'title' => 'Перший (бакалаврський)',
                'created_at' => now()
            ],
            [
                'title' => 'Другий (магістерський)',
                'created_at' => now()
            ],
            [
                'title' => 'Третій (освітньо-науковий)',
                'created_at' => now()
            ],
        ]);
    }
}
