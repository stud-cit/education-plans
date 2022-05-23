<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSemestersCreditsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('semesters_credits', function (Blueprint $table) {
          $table->id();
          $table->foreignId('subject_id')
              ->constrained()
              ->onUpdate('cascade')
              ->onDelete('cascade');
          $table->integer('credit');
          $table->integer('course');
          $table->integer('semester');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('semesters_credits');
    }
}
