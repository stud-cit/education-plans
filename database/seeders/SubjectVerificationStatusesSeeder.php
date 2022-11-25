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

        DB::table('verification_statuses')->upsert(
            [
                [
                    'id' => 2,
                    'title' => '',
                    'role_id' => 2,
                ],
                [
                    'id' => 3,
                    'title' => '',
                    'role_id' => 3,
                ],
                [
                    'id' => 4,
                    'title' => '',
                    'role_id' => 4,
                ],
                [
                    'id' => 5,
                    'title' => '',
                    'role_id' => 5,
                ]
            ],
            ['id'],
            ['role_id']
        );
    }
}
