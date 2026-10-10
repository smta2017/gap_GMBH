<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelAgencyTourOperatorTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_agency_tour_operator', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_agency_id');
            $table->unsignedBigInteger('tour_operator_id');

            $table->foreign('travel_agency_id')->references('id')->on('travel_agencies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tour_operator_id')->references('id')->on('tour_operators')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('travel_agency_tour_operator');
    }
}
