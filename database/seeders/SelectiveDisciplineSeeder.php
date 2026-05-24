<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SelectiveDisciplineSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('selective_disciplines')->insert([
            ['id' => 1, 'title' => 'Вибіркові дисципліни (каталог)'],
            ['id' => 2, 'title' => 'Вибіркові дисципліни за спеціальністю (каталог)'],
            ['id' => 3, 'title' => 'Вибіркові дисципліни за освітньою програмою (каталог)'],
        ]);
    }
}
