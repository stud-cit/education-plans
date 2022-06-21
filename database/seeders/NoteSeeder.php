<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NoteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('notes')->insert([
            ['abbreviation' => 'Т', 'explanation' => 'теоретична підготовка'],
            [
                'abbreviation' => 'Т*',
                'explanation' => 'атестаційний тиждень,проводиться в межах теоретичної підготовки'
            ],
            ['abbreviation' => 'С', 'explanation' => 'семестровий контроль (екзаменаційна сесія)'],
            ['abbreviation' => 'П', 'explanation' => 'практична підготовка'],
            ['abbreviation' => 'К', 'explanation' => 'канікули'],
            ['abbreviation' => 'А', 'explanation' => 'атестація'],
            ['abbreviation' => 'Д', 'explanation' => 'підготовка кваліфікаційної роботи']
        ]);
    }
}
