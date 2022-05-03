<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_id')->constrained()->cascadeOnUpdate()->cascadeOnDelete();
            $table->foreignId('selective_discipline_id')->nullable()->constrained('selective_disciplines')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('asu_id', 60)->nullable(true);
            $table->string('title')->nullable();
            $table->integer('credits');
            $table->integer('hours')->nullable()->default(null);
            $table->integer('practices')->nullable()->default(null);
            $table->integer('laboratories')->nullable()->default(null);
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subjects');
    }
}
