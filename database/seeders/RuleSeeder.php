<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RuleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('rules')->insert([
            ['key' => 'qualification', 'label' => 'Кваліфікація', 'date' => null, 'created_at' => now()],
            ['key' => 'qualification', 'label' => 'Освітня кваліфікація', 'date' => Date('2025-01-01'), 'created_at' => now()],
        ]);
    }
}
