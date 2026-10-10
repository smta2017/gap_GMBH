<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingCode extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('golf_courses', function (Blueprint $table) {
            $table->string('booking_code')->nullable();
            $table->string('davinci_booking_code')->nullable();
        });
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('booking_code')->nullable();
            $table->string('davinci_booking_code')->nullable();
        });
        Schema::table('dmcs', function (Blueprint $table) {
            $table->string('booking_code')->nullable();
            $table->string('davinci_booking_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        
    }
}
