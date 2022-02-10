<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plans', function (Blueprint $table) {
            $table->id();
             // $table->foreignId('form_studies_id')->nullable(true)->constrained('form_studies');
            $table->string('faculty_id', 60);
            $table->string('department_id', 60);
            $table->integer('education_level_id')->nullable(true);
            // $table->foreignId('verification_status_id')->nullable(true)->constrained('verification_statuses');
            // $table->foreignId('term_study_id')->nullable(true)->constrained('term_studies');
            $table->string('title');
            $table->integer('credits');
            $table->year('year');
            $table->integer('number_semesters');
            $table->integer('specialization_id');
            $table->string('specialization');
            $table->integer('education_program_id');
            $table->integer('qualification_id');
            $table->integer('field_knowledge_is');
            // $table->foreignId('form_organization_id')->nullable(true);
            $table->integer('count_hours');
            $table->integer('count_week');
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
        Schema::dropIfExists('plans');
    }
}
