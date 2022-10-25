<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_subjects', function (Blueprint $table) {
            $table->id();
            $table->year('year');
            $table->unsignedInteger('education_program_id')->nullable(true);
            $table->unsignedInteger('specialization_id')->nullable(true);
            $table->unsignedInteger('faculty_id')->nullable(true);
            $table->unsignedInteger('department_id')->nullable(true);
            $table->foreignId('group_id')->nullable(true)->constrained('catalog_groups');
            $table->foreignId('selective_discipline_id')->constrained();
            $table->foreignId('user_id')->constrained();
            $table->unique(['year', 'group_id']);
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
        Schema::dropIfExists('catalog_subjects');
    }
}
