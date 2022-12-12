<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSignatureTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('catalog_signature_types')->insert([
            ['id' => 1, 'title' => 'Голова'],
            ['id' => 2, 'title' => 'Керівник'],
            ['id' => 3, 'title' => 'Завідувач'],
        ]);
    }
}
