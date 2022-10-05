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
                'title' => 'дисципліни гуманітарної підготовки'
            ],
            [
                'title' => 'дисципліни іншомовної підготовки'
            ],
            [
                'title' => 'дисципліни інших освітніх програм медико-спортивного профілю'
            ],
            [
                'title' => 'дисципліни інших освітніх програм правового профілю'
            ],
            [
                'title' => 'дисципліни інших освітніх програм економічного профілю'
            ],
            [
                'title' => 'дисципліни інших освітніх програм інженерно-природничого профілю'
            ],
            [
                'title' => 'дисципліни інших освітніх програм ІТ профілю'
            ],
        ]);
    }
}
