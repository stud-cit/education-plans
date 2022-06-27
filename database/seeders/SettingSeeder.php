<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('settings')->insert([
            ['key' => 'quantity-credits-semester', 'title' => 'Кількість кредитів у семестрі', 'value' => 8],
            ['key' => 'coursework', 'title' => 'Курсові', 'value' => 1],
            ['key' => 'exam', 'title' => 'Екзаменів', 'value' => 2],
            ['key' => 'min-quantity-credits', 'title' => 'Мінімальна кількість кредитів для вивчення вибіркових дисциплін', 'value' => 2],
            ['key' => 'min-classroom-load', 'title' => 'Мінімальне аудиторне навантаження по дисципліні у відсотках', 'value' => 20],
            ['key' => 'max-classroom-load', 'title' => 'Максимальне аудиторне навантаження по дисципліні у відсотках', 'value' => 75],
        ]);
    }
}
