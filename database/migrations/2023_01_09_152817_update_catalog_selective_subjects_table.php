<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCatalogSelectiveSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalog_selective_subjects', function (Blueprint $table) {
            $table->text('general_competence', 2000)->change();
            $table->text('types_educational_activities', 2000)->change();
            $table->text('entry_requirements_applicants', 2000)->change();
            $table->text('learning_outcomes', 2000)->change();
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
            $table->string('general_competence')->change();
            $table->string('types_educational_activities')->change();
            $table->string('entry_requirements_applicants')->change();
            $table->string('learning_outcomes')->change();
        });
    }
}
