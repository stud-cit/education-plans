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
            $table->foreignId('subject_id')->constrained('catalog_selective_subjects');
            $table->boolean('status');
            $table->text('comment')->nullable(true);
            $table->timestamps();
            $table->unique(['verification_status_id', 'subject_id']);
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
            $table->dropForeign(['subject_id']);
        });

        Schema::dropIfExists('subjects_verifications');
    }
}
