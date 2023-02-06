<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReferenceCatalogSelectiveSubjects extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalog_selective_subjects', function (Blueprint $table) {
            $table->dropForeign(['catalog_education_level_id']);
            $table->foreign('catalog_education_level_id')->references('id')->on('education_levels')->onUpdate('cascade');
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
            $table->foreign('catalog_education_level_id')->references('id')->on('catalog_education_levels')->onUpdate('cascade');
        });
    }
}
