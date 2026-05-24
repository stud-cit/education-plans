<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('catalog_groups')->insert([
            [
                'id' => 1,
                'title' => 'дисципліни гуманітарної підготовки'
            ],
            [
                'id' => 2,
                'title' => 'дисципліни іншомовної підготовки'
            ],
            [
                'id' => 3,
                'title' => 'дисципліни інших освітніх програм медико-спортивного профілю'
            ],
            [
                'id' => 4,
                'title' => 'дисципліни інших освітніх програм правового профілю'
            ],
            [
                'id' => 5,
                'title' => 'дисципліни інших освітніх програм економічного профілю'
            ],
            [
                'id' => 6,
                'title' => 'дисципліни інших освітніх програм інженерно-природничого профілю'
            ],
            [
                'id' => 7,
                'title' => 'дисципліни інших освітніх програм ІТ профілю'
            ],
        ]);
    }
}
