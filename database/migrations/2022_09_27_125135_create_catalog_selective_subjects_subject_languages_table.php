<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogSelectiveSubjectsSubjectLanguagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_selective_subjects_subject_languages', function (Blueprint $table) {
            $table->id();
            $table->foreignId('language_id')->constrained('subject_languages');
            $table->foreignId('subject_id')->constrained('catalog_selective_subjects');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('catalog_selective_subjects_subject_languages');
    }
}