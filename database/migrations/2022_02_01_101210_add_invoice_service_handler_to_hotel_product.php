<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInvoiceServiceHandlerToHotelProduct extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('hotel_products', function (Blueprint $table) {

            $table->string('booking_from_id')->nullable()->after('validity_to');
            $table->string('booking_possible_for')->nullable()->after('validity_to');

            $table->unsignedBigInteger('service_handler_id')->nullable()->after('validity_to');
            $table->unsignedBigInteger('service_handler_type_id')->nullable()->after('validity_to');

            $table->unsignedBigInteger('invoice_handler_id')->nullable()->after('validity_to');

            $table->boolean('use_service_configurations')->nullable()->default(0)->after('validity_to');
            
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('hotel_products', function (Blueprint $table) {
            //
        });
    }
}
