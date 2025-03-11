<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class VerificationStatusesProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('verification_statuses')->insert(
            ['title' => 'Інститут/факультет', 'type' => 'project', 'role_id' => User::FACULTY_INSTITUTE],
        );
    }
}
