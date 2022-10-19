<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogSelectiveSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_selective_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalog_subject_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->string('asu_id', 60);
            $table->text('title');
            $table->text('title_en')->nullable(true);
            $table->json('list_fields_knowledge');
            $table->unsignedInteger('faculty_id');
            $table->unsignedInteger('department_id');
            $table->string('general_competence');
            $table->string('learning_outcomes');
            $table->string('types_educational_activities');
            $table->string('number_acquirers');
            $table->string('entry_requirements_applicants');
            $table->json('limitation');
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
        Schema::disableForeignKeyConstraints();
        Schema::dropIfExists('catalog_selective_subjects');
    }
}
