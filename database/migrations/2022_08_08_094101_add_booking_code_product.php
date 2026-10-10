<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBookingCodeProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('products', function (Blueprint $table) {
            $table->string('booking_code')->nullable();
            $table->string('davinci_booking_code')->nullable();
        });
        Schema::table('product_services', function (Blueprint $table) {
            $table->string('booking_code')->nullable();
            $table->string('davinci_booking_code')->nullable();
        });
        Schema::table('hotels', function (Blueprint $table) {
            $table->string('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('products', function (Blueprint $table) {
            //
        });
    }
}
