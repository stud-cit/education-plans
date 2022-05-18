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
        DB::table('hours_modules')->insert([
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => 2,
            'course' => 1,
            'semester' => 1,
            'module' => 1
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => 2,
            'course' => 1,
            'semester' => 1,
            'module' => 2
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => 2,
            'course' => 1,
            'semester' => 2,
            'module' => 3
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => 2,
            'course' => 1,
            'semester' => 2,
            'module' => 4
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => 2,
            'course' => 2,
            'semester' => 3,
            'module' => 1
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => 2,
            'course' => 2,
            'semester' => 3,
            'module' => 2
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => null,
            'form_control_id' => null,
            'hour' => 2,
            'course' => 2,
            'semester' => 4,
            'module' => 3
          ],
          [
            'subject_id' => 1,
            'individual_task_id' => 1,
            'form_control_id' => 1,
            'hour' => 2,
            'course' => 2,
            'semester' => 4,
            'module' => 4
          ],
        ]);
    }
}
