<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePlanVerifivationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('plan_verifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')
              ->constrained('plans')
              ->cascadeOnUpdate()
              ->cascadeOnDelete();
            $table->foreignId('user_id')->constrained('users');
            $table->foreignId('verification_statuses_id')->constrained('verification_statuses');
            $table->boolean('status');
            $table->text('comment')->nullable();
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
        Schema::dropIfExists('plan_verifivation');
    }
}
