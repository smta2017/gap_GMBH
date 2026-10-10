<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGolfHolidayHotelTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('golf_holiday_hotel', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('golf_holiday_id');
            $table->unsignedBigInteger('hotel_id');

            $table->foreign('golf_holiday_id')->references('id')->on('golf_holidays')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('hotel_id')->references('id')->on('hotels')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('golf_holiday_hotel');
    }
}
