<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogHelperTypesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('catalog_helper_types')->insert([
            [
                'title' => 'Компетентності (загальні та/або фахові,  на розвиток яких спрямована дисципліна',
                'key' => 'catalog'
            ],
            [
                'title' => 'Результати навчання за навчальною дисципліною',
                'key' => 'catalog'
            ],
            [
                'title' => 'Види навчальних занять та методи викладання, що пропонуються',
                'key' => 'catalog'
            ],
            [
                'title' => 'Вхідні вимоги до здобувачів, які хочуть обрати дисципліну/вимоги до матеріально-технічного забезпечення',
                'key' => 'catalog'
            ],

        ]);
    }
}
