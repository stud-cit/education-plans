<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerificationStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('verification_statuses')->Insert([
          [
              'title' => 'Освітня програма',
          ],
          [
              'title' => 'Навчальний відділ',
          ],
          [
              'title' => 'Відділ практики',
          ],
          [
              'title' => 'Навчально-методичний відділ',
          ]
      ]);
    }
}
