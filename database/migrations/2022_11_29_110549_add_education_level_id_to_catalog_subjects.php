<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddEducationLevelIdToCatalogSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalog_subjects', function (Blueprint $table) {
            $table
                ->foreignId('catalog_education_level_id')
                ->nullable(true)
                ->after('user_id')
                ->constrained('catalog_education_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_subjects', function (Blueprint $table) {
            $table->dropForeign(['education_level_id']);
        });
    }
}
