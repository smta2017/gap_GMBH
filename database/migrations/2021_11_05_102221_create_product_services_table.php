<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductServicesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('type');

            $table->unsignedBigInteger('company_type_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            
            $table->unsignedBigInteger('provider_id')->nullable();

            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();

            $table->string('letter_code')->nullable();

            $table->string('code')->nullable();
            $table->string('ref_code')->nullable();

            $table->date('validity_from')->nullable();
            $table->date('validity_to')->nullable();

            $table->unsignedBigInteger('invoice_handler_id')->nullable();

            $table->unsignedBigInteger('service_handler_type_id')->nullable();
            $table->unsignedBigInteger('service_handler_id')->nullable();

            $table->string('booking_possible_for')->nullable();
            $table->string('booking_from_id')->nullable();

            $table->boolean('active')->defautl('1')->nullable();

            $table->timestamps();

            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_services');
    }
}
