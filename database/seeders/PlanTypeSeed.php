<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlanTypeSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('plan_types')->insert([
            ['title' => 'Шаблон'],
            ['title' => 'План'],
            ['title' => 'Скорочений план'],
        ]);
    }
}
