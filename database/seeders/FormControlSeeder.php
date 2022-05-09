<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class FormControlSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('form_controls')->insert([
          'title' => 'Залік',
        ]);
        DB::table('form_controls')->insert([
          'title' => 'Диференційний залік',
        ]);
        DB::table('form_controls')->insert([
          'title' => 'Іспит',
        ]);
        DB::table('form_controls')->insert([
          'title' => 'Без атестацій',
        ]);
    }
}
