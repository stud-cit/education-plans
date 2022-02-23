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
            'cycle_id' => 1,
            'selective_discipline_id' => 1,
            'title' => 'Іноземна мова',
            'credits' => 30,
            'hours' => 5,
            'practices' => 12,
            'laboratories' => 13
        ]);
    }
}
