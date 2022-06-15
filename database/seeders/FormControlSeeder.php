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
          'id' => 1,
          'title' => 'Іспит',
        ]);
        DB::table('form_controls')->insert([
          'id' => 2,
          'title' => 'Диференційний залік',
        ]);
        DB::table('form_controls')->insert([
          'id' => 3,
          'title' => 'Залік',
        ]);
        DB::table('form_controls')->insert([
          'id' => 8,
          'title' => 'Захист',
        ]);
        DB::table('form_controls')->insert([
          'id' => 10,
          'title' => 'Без атестацій',
        ]);
    }
}
