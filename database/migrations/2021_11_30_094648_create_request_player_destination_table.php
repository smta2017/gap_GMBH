<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestPlayerDestinationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_player_destination', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_destination_id');
            $table->unsignedBigInteger('request_player_id');
            $table->timestamps();

            $table->foreign('request_destination_id')->references('id')->on('request_destinations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('request_player_id')->references('id')->on('request_players')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_player_destination');
    }
}
