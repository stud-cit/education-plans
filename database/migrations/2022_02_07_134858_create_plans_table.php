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
            $table->string('guid');
            $table->foreignId('author_id')->constrained('users', 'id');
            $table->foreignId('parent_id')->nullable()->constrained('plans', 'id');
            $table->integer('faculty_id')->nullable();
            $table->integer('department_id')->nullable();
            // $table->foreignId('study_term_id')->nullable(true)->constrained('term_studies');
            $table->string('title');
            $table->integer('credits');
            $table->year('year');
            $table->integer('number_semesters');
            $table->integer('speciality_id')->nullable(true);
            $table->integer('specialization_id')->nullable(true);
            $table->integer('education_program_id')->nullable(true);
            $table->integer('program_op_id')->nullable(true);
            $table->integer('qualification_id')->nullable(true);
            $table->integer('field_knowledge_id')->nullable(true);
            // $table->foreignId('form_organization_id')->nullable(true);
            $table->json('hours_weeks_semesters')->nullable(true);
            $table->json('summary_data_budget_time')->nullable(true);
            $table->json('practical_training')->nullable(true);
            $table->json('schedule_education_process')->nullable(true);
            $table->boolean('published')->default(false);

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
