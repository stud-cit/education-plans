<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCatalogHelperTypeIdToSubjectHelpersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('subject_helpers', function (Blueprint $table) {
            $table->foreignId('catalog_helper_type_id')->constrained();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('subject_helpers', function (Blueprint $table) {
            $table->dropForeign(['catalog_helper_type_id']);
            $table->dropColumn(['catalog_helper_type_id']);
        });
    }
}
