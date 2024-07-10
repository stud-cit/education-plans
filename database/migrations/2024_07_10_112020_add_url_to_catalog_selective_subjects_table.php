<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddUrlToCatalogSelectiveSubjectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('catalog_selective_subjects', function (Blueprint $table) {
            $table->string('url', 2048)->nullable()->after('limitation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('catalog_selective_subjects', function (Blueprint $table) {
            $table->dropColumn('url');
        });
    }
}
