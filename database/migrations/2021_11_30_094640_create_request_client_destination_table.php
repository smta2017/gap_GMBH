<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestClientDestinationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_client_destination', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('request_destination_id');
            $table->unsignedBigInteger('request_client_id');
            $table->timestamps();

            $table->foreign('request_destination_id')->references('id')->on('request_destinations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('request_client_id')->references('id')->on('request_clients')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_client_destination');
    }
}
