<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ListCycleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('list_cycles')->insert([
            ['title' => 'ЦИКЛ ДИСЦИПЛІН ЗАГАЛЬНОЇ ПІДГОТОВКИ', 'general' => true],
            ['title' => 'Обов\'язкові навчальні дисципліни', 'general' => false],
            ['title' => 'Вибіркові навчальні дисципліни', 'general' => false],
            ['title' => 'ЦИКЛ ДИСЦИПЛІН ПРОФЕСІЙНОЇ ПІДГОТОВКИ', 'general' => true],
            ['title' => 'Обов\'язкові навчальні дисципліни за спеціальністю', 'general' => false],
            ['title' => 'Обов\'язкові навчальні дисципліни за освітньою програмою', 'general' => false],
            ['title' => 'Вибіркові навчальні дисципліни за спеціальністю', 'general' => false],
            ['title' => 'Вибіркові навчальні дисципліни за освітньою програмою', 'general' => false],
            ['title' => 'ЦИКЛ ПРАКТИЧНОЇ ПІДГОТОВКИ', 'general' => true],
            ['title' => 'АТЕСТАЦІЯ', 'general' => true],
            ['title' => 'ЦИКЛ НАУКОВО-ДОСЛІДНОЇ ПІДГОТОВКИ', 'general' => true],
        ]);
    }
}
