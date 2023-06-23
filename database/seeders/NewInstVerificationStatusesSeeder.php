<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NewInstVerificationStatusesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('verification_statuses')->insert(
            ['title' => 'Інститут/факультет', 'type' => 'plan', 'role_id' => 6, 'order' => 1]
        );

        $ids = [1, 2, 3, 4, 5];

        foreach ($ids as $id) {
            if ($id === 1) {
                DB::table('verification_statuses')->where('id', $id)
                    ->update(['order' =>  0]);
            } else {

                DB::table('verification_statuses')->where('id', $id)
                    ->update(['order' =>  $id]);
            }
        }
    }
}
