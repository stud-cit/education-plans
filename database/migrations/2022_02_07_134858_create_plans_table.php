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
            $table->integer('faculty_id');
            $table->integer('department_id');
            // $table->foreignId('verification_status_id')->nullable(true)->constrained('verification_statuses');
            // $table->foreignId('term_study_id')->nullable(true)->constrained('term_studies');
            $table->string('title');
            $table->integer('credits');
            $table->year('year');
            $table->integer('number_semesters');
            $table->integer('specialization_id')->nullable(true);
            $table->string('specialization')->nullable(true);
            $table->integer('education_program_id');
            $table->integer('qualification_id');
            $table->integer('field_knowledge_id');
            // $table->foreignId('form_organization_id')->nullable(true);
            $table->integer('count_hours');
            $table->integer('count_week');
            $table->json('hours_week')->nullable(true);
            $table->json('schedule_education_process')->nullable(true);
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
