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
                'type' => 'subject',
                'role_id' => 6,
            ],
            [
                'title' => 'Навчальний відділ',
                'type' => 'subject',
                'role_id' => 2,
            ],
            [
                'title' => 'Заступник НМВ',
                'type' => 'subject',
                'role_id' => 4,
            ],
            [
                'title' => 'Начальник НМВ',
                'type' => 'subject',
                'role_id' => 5,
            ]
        ]);
    }
}
