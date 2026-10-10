<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectServiceFeeDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_service_fee_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('service_fees_details_id');
            $table->unsignedBigInteger('service_addon_id')->nullable();
            
            $table->integer('qty')->nullable();
            $table->integer('fees')->nullable();

            $table->string('unit')->nullable();

            $table->string('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('object_service_fee_details');
    }
}
