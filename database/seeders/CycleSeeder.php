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
            ['title' => 'Цикл дисциплін загальної підготовки', 'cycle_id' => null, 'credit' => 30],
            ['title' => 'Обов\'язкові навчальні дисципліни', 'cycle_id' => 1, 'credit' => 1],
            ['title' => 'Вибіркові навчальні дисципліни','cycle_id' => 1, 'credit' => 0],
            ['title' => 'Цикл дисциплін професійної підготовки', 'cycle_id' => null, 'credit' => 20],
            ['title' => 'Обов\'язкові навчальні дисципліни', 'cycle_id' => 2, 'credit' => 0],
            ['title' => 'Обов\'язкові навчальні дисципліни за спеціальністю', 'cycle_id' => 5, 'credit' => 0],
            ['title' => 'Обов\'язкові навчальні дисципліни за освітньою програмою', 'cycle_id' => 5, 'credit' => 0],
            ['title' => 'Вибіркові навчальні дисципліни', 'cycle_id' => 2, 'credit' => 0],
            ['title' => 'Вибіркові навчальні дисципліни за спеціальністю', 'cycle_id' => 8, 'credit' => 0],
            ['title' => 'Вибіркові навчальні дисципліни за освітньою програмою', 'cycle_id' => 8, 'credit' => 0],
            ['title' => 'Цикл практичної підготовки', 'cycle_id' => null, 'credit' => 0],
            ['title' => 'Атестація', 'cycle_id' => null, 'credit' => 0],
        ]);
    }
}
