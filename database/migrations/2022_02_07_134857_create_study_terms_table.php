<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStudyTermsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('study_terms', function (Blueprint $table) {
            $table->id();
            $table->string('title')->unique()->nullable(false);
            $table->integer('year');
            $table->integer('month');
            $table->integer('course');
            $table->integer('module');
            $table->integer('number_semesters');
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
        Schema::dropIfExists('study_terms');
    }
}
