<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCatalogSignaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('catalog_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('asu_id', 60);
            $table->foreignId('catalog_subject_id')->constrained();
            $table->foreignId('catalog_signature_type_id')->constrained();
            $table->unsignedInteger('faculty_id');
            $table->unsignedInteger('department_id');
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
        Schema::dropIfExists('catalog_signatures');
    }
}
