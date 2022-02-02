<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CycleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('cycles')->insert([
            ['title' => 'Цикл дисциплін загальної підготовки', 'cycle_id' => null],
            ['title' => 'Обов\'язкові навчальні дисципліни', 'cycle_id' => 1],
            ['title' => 'Вибіркові навчальні дисципліни','cycle_id' => 1],
            ['title' => 'Цикл дисциплін професійної підготовки', 'cycle_id' => null],
            ['title' => 'Обов\'язкові навчальні дисципліни', 'cycle_id' => 2],
            ['title' => 'Обов\'язкові навчальні дисципліни за спеціальністю', 'cycle_id' => 5],
            ['title' => 'Обов\'язкові навчальні дисципліни за освітньою програмою', 'cycle_id' => 5],
            ['title' => 'Вибіркові навчальні дисципліни', 'cycle_id' => 2],
            ['title' => 'Вибіркові навчальні дисципліни за спеціальністю', 'cycle_id' => 8],
            ['title' => 'Вибіркові навчальні дисципліни за освітньою програмою', 'cycle_id' => 8],
            ['title' => 'Цикл практичної підготовки', 'cycle_id' => null],
            ['title' => 'Атестація', 'cycle_id' => null],
        ]);
    }
}
