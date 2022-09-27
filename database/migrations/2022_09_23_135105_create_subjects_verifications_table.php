<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubjectsVerificationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subjects_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('verification_status_id')->constrained();
            $table->foreignId('catalog_selective_subject_id')->constrained();
            $table->boolean('status');
            $table->text('comment');
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
        Schema::table('subjects_verifications', function (Blueprint $table) {
            $table->dropForeign(['user_id']);
            $table->dropForeign(['verification_status_id']);
            $table->dropForeign(['catalog_selective_subject_id']);
        });

        Schema::dropIfExists('subjects_verifications');
    }
}
