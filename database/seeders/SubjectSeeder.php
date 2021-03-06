<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('subjects')->insert([
        'cycle_id' => 2,
        'asu_id' => 1,
        // 'title' => 'Іноземна мова',
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
      DB::table('subjects')->insert([
        'cycle_id' => 2,
        'asu_id' => 2,
        // 'title' => 'Українознавство зі змістовим модулем "Комунікативний курс української мови"',
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
      DB::table('subjects')->insert([
        'cycle_id' => 2,
        'asu_id' => 3,
        // 'title' => 'Філософія',
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
     
      DB::table('subjects')->insert([
        'cycle_id' => 7,
        'asu_id' => 4,
        // 'title' => 'Іноземна мова',
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
      DB::table('subjects')->insert([
        'cycle_id' => 3,
        'asu_id' => 5,
        // 'title' => 'Фізичне виховання (вибір форм рухової активності)',
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
      DB::table('subjects')->insert([
        'cycle_id' => 3,
        'asu_id' => 6,
        // 'title' => 'Вибіркові дисципліни гуманітарного спрямування (додаток 1)',
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
      DB::table('subjects')->insert([
        'cycle_id' => 3,
        'asu_id' => 7,
        // 'title' => 'Вибіркові дисципліни інших спеціальностей  (додаток 2)',
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
      DB::table('subjects')->insert([
        'cycle_id' => 3,
        'asu_id' => 8,
        // 'title' => 'Іноземна мова',
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
      DB::table('subjects')->insert([
        'cycle_id' => 9,
        'selective_discipline_id' => 2,
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
      DB::table('subjects')->insert([
        'cycle_id' => 10,
        'selective_discipline_id' => 3,
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
      DB::table('subjects')->insert([
        'cycle_id' => 11,
        'asu_id' => 9,
        // 'title' => 'Практика',
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
      DB::table('subjects')->insert([
        'cycle_id' => 12,
        'asu_id' => 10,
        // 'title' => 'Кваліфікаційна робота бакалавра',
        'credits' => 0,
        'hours' => 0,
        'practices' => 0,
        'laboratories' => 0
      ]);
    }
}
