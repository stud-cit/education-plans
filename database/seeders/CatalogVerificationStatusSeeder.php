<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogVerificationStatusSeeder extends Seeder
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
                'type' => 'speciality',
                'role_id' => 6,
            ],
            [
                'title' => 'Інститут/факультет',
                'type' => 'education-program',
                'role_id' => 6,
            ],

        ]);
    }
}
