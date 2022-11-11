<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectVerificationStatusesSeeder extends Seeder
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
                'title' => 'Інститут/факультет',
                'type' => 'subject'
            ],
            [
                'title' => 'Навчальний відділ',
                'type' => 'subject'
            ],
            [
                'title' => 'Навчально-методичний відділ',
                'type' => 'subject'
            ],
        ]);
    }
}
