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
                'id' => 1,
                'title' => 'Освітня програма',
                'type' => 'plan',
                'role_id' => null,
                'order' => 0,
                'deleted_at' => '2024-04-03 16:23:22',
            ],
            [
                'id' => 2,
                'title' => 'Навчальний відділ',
                'type' => 'plan',
                'role_id' => 2,
                'order' => 2,
                'deleted_at' => null,
            ],
            [
                'id' => 3,
                'title' => 'Відділ практики',
                'type' => 'plan',
                'role_id' => 3,
                'order' => 3,
                'deleted_at' => null,
            ],
            [
                'id' => 4,
                'title' => 'НМ відділ',
                'type' => 'plan',
                'role_id' => 4,
                'order' => 4,
                'deleted_at' => null,
            ],
            [
                'id' => 5,
                'title' => 'Начальник НМВ',
                'type' => 'plan',
                'role_id' => 5,
                'order' => 5,
                'deleted_at' => '2024-04-03 16:23:22',
            ],
            [
                'id' => 6,
                'title' => 'Інститут/факультет',
                'type' => 'subject',
                'role_id' => 6,
                'order' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 7,
                'title' => 'Навчальний відділ',
                'type' => 'subject',
                'role_id' => 2,
                'order' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 8,
                'title' => 'Заступник НМВ',
                'type' => 'subject',
                'role_id' => 4,
                'order' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 9,
                'title' => 'Начальник НМВ',
                'type' => 'subject',
                'role_id' => 5,
                'order' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 10,
                'title' => 'Інститут/факультет',
                'type' => 'speciality',
                'role_id' => 6,
                'order' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 11,
                'title' => 'Інститут/факультет',
                'type' => 'education-program',
                'role_id' => 6,
                'order' => null,
                'deleted_at' => null,
            ],
            [
                'id' => 12,
                'title' => 'Інститут/факультет',
                'type' => 'plan',
                'role_id' => 6,
                'order' => 1,
                'deleted_at' => null,
            ],
            [
                'id' => 13,
                'title' => 'Інститут/факультет',
                'type' => 'project',
                'role_id' => 6,
                'order' => 1,
                'deleted_at' => null,
            ],
        ]);
    }
}
