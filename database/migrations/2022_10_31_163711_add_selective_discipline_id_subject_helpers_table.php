<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSelectiveDisciplineIdSubjectHelpersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subject_helpers', function (Blueprint $table) {
            $table->foreignId('selective_discipline_id')->default(1)->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subject_helpers', function (Blueprint $table) {
            $table->dropForeign(['selective_discipline_id']);
        });
    }
}
