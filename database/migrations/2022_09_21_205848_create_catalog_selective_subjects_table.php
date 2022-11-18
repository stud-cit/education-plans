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
            $table->foreignId('catalog_subject_id')->constrained('catalog_subjects', 'id');
            $table->foreignId('user_id')->constrained();
            $table->integer('asu_id')->nullable(true); // 1
            $table->text('title')->nullable(true); // 1
            $table->text('title_en')->nullable(true); // 1
            // language in separate table 2
            // education_level_id  3
            $table->json('list_fields_knowledge'); // 4
            $table->unsignedInteger('faculty_id');
            $table->unsignedInteger('department_id'); // 5
            // 6 teachers // type ??
            // 7 teachers // type ??
            $table->string('general_competence'); // 8
            $table->string('learning_outcomes'); // 9
            $table->string('types_educational_activities'); // 10
            $table->string('number_acquirers'); // 11
            $table->string('entry_requirements_applicants'); // 12
            $table->json('limitation'); // 13
            $table->boolean('published')->default(false);
            $table->boolean('need_verification')->default(false);
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
