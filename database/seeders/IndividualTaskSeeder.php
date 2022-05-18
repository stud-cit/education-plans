<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IndividualTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('individual_tasks')->insert([
          'title' => 'Контрольна робота',
        ]);
        DB::table('individual_tasks')->insert([
          'title' => 'Курсова робота',
        ]);
        DB::table('individual_tasks')->insert([
          'title' => 'Без завдання',
        ]);
    }
}
