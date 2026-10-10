<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_products', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('hotel_id')->nullable();

            $table->string('code')->nullable();
            $table->string('ref_code')->nullable();

            $table->unsignedBigInteger('room_type_id')->nullable();
            $table->unsignedBigInteger('room_view_id')->nullable();
            $table->unsignedBigInteger('room_board_id')->nullable();

            $table->date('validity_from')->nullable();
            $table->date('validity_to')->nullable();

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
        Schema::dropIfExists('hotel_products');
    }
}
