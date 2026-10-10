<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGolfHolidaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('golf_holidays', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('hotel_id')->nullable();

            $table->string('code')->nullable();
            $table->string('ref_code')->nullable();

            $table->unsignedBigInteger('room_type_id')->nullable();
            $table->unsignedBigInteger('room_view_id')->nullable();
            $table->unsignedBigInteger('room_board_id')->nullable();

            $table->integer('number_of_nights')->nullable();
            $table->integer('number_of_guests')->nullable();
            $table->integer('number_of_rounds')->nullable();
            $table->integer('number_of_golf_courses')->nullable();

            $table->boolean('unlimited_rounds')->nullable()->default(0);
            
            $table->date('validity_from')->nullable();
            $table->date('validity_to')->nullable();

            $table->boolean('use_service_configurations')->nullable()->default(0);
            
            $table->unsignedBigInteger('invoice_handler_id')->nullable();

            $table->unsignedBigInteger('service_handler_type_id')->nullable();
            $table->unsignedBigInteger('service_handler_id')->nullable();

            $table->string('booking_possible_for')->nullable();
            $table->string('booking_from_id')->nullable();

            $table->boolean('status')->default(1);

            $table->timestamps();

            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('golf_holidays');
    }
}
