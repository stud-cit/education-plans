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
            ['title' => 'Вибіркові дисципліни (каталог)'],
            ['title' => 'Вибіркові дисципліни за спеціальністю (каталог)'],
            ['title' => 'Вибіркові дисципліни за освітньою програмою (каталог)'],
        ]);
    }
}
