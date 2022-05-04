<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class UserActivityLogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('user_activity_logs')->insert([
            'asu_id' => '044fd9f4-83c3-e511-867d-001a4be6d04a',
            'user_name' => 'Петро',
            'user_role' => 'Адміністратор',
            'operation' => 'Видалення плану',
            'ip' => '192.168.1.1',
           
            
        ]);
    }
}
