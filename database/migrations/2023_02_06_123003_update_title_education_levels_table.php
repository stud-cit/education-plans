<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class UpdateTitleEducationLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        DB::table('education_levels')->where('id', 2)
            ->update(['title' => 'перший (бакалаврський) рівень']);

        DB::table('education_levels')->where('id', 4)
            ->update(['title' => 'другий (магістерський) рівень']);

        DB::table('education_levels')->where('id', 8)
            ->update(['title' => 'третій (освітньо-науковий) рівень']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    }
}
