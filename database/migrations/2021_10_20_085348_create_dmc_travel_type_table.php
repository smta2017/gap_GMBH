<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmcTravelTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmc_travel_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmc_id');
            $table->unsignedBigInteger('travel_type_id');

            $table->foreign('dmc_id')->references('id')->on('dmcs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('travel_type_id')->references('id')->on('travel_types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dmc_travel_type');
    }
}
