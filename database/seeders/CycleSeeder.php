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
			[
				// 'title' => '1 Цикл дисциплін загальної підготовки',
				'cycle_id' => null,
                'list_cycle_id' => 1,
				'plan_id' => 1,
				'credit' => 65
			],
			[
				// 'title' => '1.1 Обов\'язкові навчальні дисципліни',
				'cycle_id' => 1,
                'list_cycle_id' => 2,
				'plan_id' => 1,
				'credit' => 50
			],
			[
				// 'title' => '1.2 Вибіркові навчальні дисципліни',
				'cycle_id' => 1,
                'list_cycle_id' => 3,
				'plan_id' => 1,
				'credit' => 15
			],
			[
				// 'title' => '2 Цикл дисциплін професійної підготовки',
				'cycle_id' => null,
                'list_cycle_id' => 4,
				'plan_id' => 1,
				'credit' => 160
			],
			[
				// 'title' => '2.1 Обов\'язкові навчальні дисципліни',
				'cycle_id' => 4,
                'list_cycle_id' => 2,
				'plan_id' => 1,
				'credit' => 115
			],
			[
				// 'title' => '2.1.1 Обов\'язкові навчальні дисципліни за спеціальністю',
				'cycle_id' => 5,
                'list_cycle_id' => 5,
				'plan_id' => 1,
				'credit' => 55
			],
			[
				// 'title' => '2.1.2 Обов\'язкові навчальні дисципліни за освітньою програмою',
				'cycle_id' => 5,
                'list_cycle_id' => 6,
				'plan_id' => 1,
				'credit' => 60
			],
			[
				// 'title' => '2.2 Вибіркові навчальні дисципліни',
				'cycle_id' => 4,
                'list_cycle_id' => 3,
				'plan_id' => 1,
				'credit' => 45
			],
			[
				// 'title' => '2.2.1 Вибіркові навчальні дисципліни за спеціальністю',
				'cycle_id' => 8,
                'list_cycle_id' => 7,
				'plan_id' => 1,
				'credit' => 10
			],
			[
				// 'title' => '2.2.2 Вибіркові навчальні дисципліни за освітньою програмою',
				'cycle_id' => 8,
                'list_cycle_id' => 8,
				'plan_id' => 1,
				'credit' => 35
			],
			[
				// 'title' => '3 Цикл практичної підготовки',
				'cycle_id' => null,
                'list_cycle_id' => 9,
				'plan_id' => 1,
				'credit' => 10
			],
			[
				// 'title' => '4 Атестація',
				'cycle_id' => null,
                'list_cycle_id' => 10,
				'plan_id' => 1,
				'credit' => 5
			]
		]);
	}
}
