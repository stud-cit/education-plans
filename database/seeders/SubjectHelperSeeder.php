<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SubjectHelperSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('subject_helpers')->insert([
            [
                'title' => 'Здатність до абстрактного мислення, аналізу та синтезу. Здатність діяти соціально відповідально та свідомо',
                'catalog_helper_type_id' => 1
            ],
            [
                'title' => 'Застосовувати категорії та принципи виборчого права татехнології виборчих кампаній для оцінювання програм кандидатів тадемократичності виборів. Давати оцінку перевагам та недолікам виборчих систем. Узагальнювати електоральну статистику',
                'catalog_helper_type_id' => 2
            ],
            [
                'title' => 'Лекції, семінари, проблемні заняття',
                'catalog_helper_type_id' => 3
            ],
            [
                'title' => 'Аудиторiя',
                'catalog_helper_type_id' => 4
            ],
        ]);
    }
}
