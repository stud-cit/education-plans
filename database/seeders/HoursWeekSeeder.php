<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HoursWeekSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('hours_weeks')->insert([
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => '2'
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => '2'
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => '2'
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => '2'
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => '2'
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => '2'
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => '2'
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => 1,
            'form_control_id' => 1,
            'hour' => '2'
          ],
        ]);
    }
}
