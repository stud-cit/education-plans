<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogGroupsHasCatalogSelectiveSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_groups_has_catalog_selective_subjects', function (Blueprint $table) {
            $table->id();
            $table->foreignId('subject_id')->constrained('catalog_selective_subjects');
            $table->foreignId('group_id')->constrained('catalog_groups');
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
        Schema::dropIfExists('catalog_groups_has_catalog_selective_subjects');
    }
}
