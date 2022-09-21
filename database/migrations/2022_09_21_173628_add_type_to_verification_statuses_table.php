<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTypeToVerificationStatusesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('verification_statuses', function (Blueprint $table) {
            $table->string('type')->after('title')->default('plan'); // type: plan, catalog, subject
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('verification_statuses', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
}
