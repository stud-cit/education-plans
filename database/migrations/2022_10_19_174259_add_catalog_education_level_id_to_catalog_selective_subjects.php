<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCatalogEducationLevelIdToCatalogSelectiveSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalog_selective_subjects', function (Blueprint $table) {
            $table->foreignId('catalog_education_level_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_selective_subjects', function (Blueprint $table) {
            $table->dropForeign(['catalog_education_level_id']);
        });
    }
}
