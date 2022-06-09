<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('positions')->insert([
            ['position' => 'Директор навчально-наукового інституту бізнесу, економіки та менеджменту'],
            ['position' => 'Завідувач кафедри фінансів, банківської справи та страхування'],
            ['position' => 'Керівник робочої проектної групи освітньої програми Фінанси та облік в підприємництві'],
            ['position' => 'Керівник робочої проектної групи освітньої програми Державні та місцеві фінанси'],
            ['position' => 'Керівник робочої проектної групи освітньої програми Банківська справа'],
            ['position' => 'Проректор з науково-педагогічної діяльності'],
        ]);
    }
}
