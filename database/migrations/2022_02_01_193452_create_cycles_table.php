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
            $table->string('title');
            // $table->unsignedBigInteger('cycle_id')->nullable()->default(null);
            // $table->foreignIdFor(Cycle::class)->nullable()->default(null);
            $table->foreignId('cycle_id')->nullable()->default(null)->constrained('cycles');
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
