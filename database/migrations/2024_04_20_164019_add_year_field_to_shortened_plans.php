<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddYearFieldToShortenedPlans extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('shortened_plans', function (Blueprint $table) {
            $table->year('year')->nullable()->after('shortened_by_year');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('shortened_plans', function (Blueprint $table) {
            $table->dropColumn('year');
            $table->dropSoftDeletes();
        });
    }
}
