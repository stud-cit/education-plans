<?php

use App\Models\Cycle;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCyclesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cycle_id')
                ->nullable(true)
                ->constrained('cycles')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('list_cycle_id')->constant();
            // $table->string('title');
            $table->integer('credit')->nullable(true);
            $table->boolean('has_discipline')->default(true);
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
        Schema::dropIfExists('cycles');
    }
}
