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
            ['title' => 'Цикл дисциплін загальної підготовки', 'number' => 1, 'cycle_id' => null],
            ['title' => 'Обов\'язкові навчальні дисципліни', 'number' => 1.1, 'cycle_id' => 1],
            ['title' => 'Вибіркові навчальні дисципліни', 'number' => 1.2, 'cycle_id' => 1],
            ['title' => 'Вибіркові навчальні дисципліни', 'number' => 1.2, 'cycle_id' => 1],
            ['title' => 'Цикл дисциплін професійної підготовки', 'number' => 2, 'cycle_id' => null],
            ['title' => 'Обов\'язкові навчальні дисципліни', 'number' => 2.1, 'cycle_id' => 2]
        ]);
    }
}
