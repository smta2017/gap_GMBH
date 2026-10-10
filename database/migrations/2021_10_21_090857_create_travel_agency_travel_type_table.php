<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTravelAgencyTravelTypeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('travel_agency_travel_type', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('travel_agency_id');
            $table->unsignedBigInteger('travel_type_id');

            $table->foreign('travel_agency_id')->references('id')->on('travel_agencies')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('travel_agency_travel_type');
    }
}
