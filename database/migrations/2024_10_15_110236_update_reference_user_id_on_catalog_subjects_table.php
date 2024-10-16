<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateReferenceUserIdOnCatalogSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalog_subjects', function (Blueprint $table) {

            $table->unsignedBigInteger('user_id')->nullable(true)->change();
            $table->dropForeign(['user_id']);
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_subjects', function (Blueprint $table) {
            $table->unsignedBigInteger('user_id')->change();
        });
    }
}
