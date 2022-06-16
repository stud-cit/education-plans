<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSignaturesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('signatures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('plan_id')
                ->nullable(true)
                ->constrained('plans')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('position_id')
                ->nullable(true)
                ->constrained('positions')
                ->cascadeOnUpdate();
            $table->string('asu_id', 60);
            $table->unique(['plan_id', 'position_id', 'asu_id']);
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
        Schema::dropIfExists('signatures');
    }
}
