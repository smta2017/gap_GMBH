<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_products', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('request_destination_id');
            $table->unsignedBigInteger('product_id');

            $table->string('name')->nullable();

            $table->boolean('is_package')->default(0);

            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('golf_course_id')->nullable();

            $table->string('code')->nullable();
            $table->string('ref_code')->nullable();
            $table->unsignedBigInteger('tee_time_id')->nullable();
            $table->unsignedBigInteger('hole_id')->nullable();

            $table->boolean('junior')->nullable()->default(1);
            $table->boolean('multi_players_only')->nullable()->default(1);
            $table->boolean('buggy')->nullable()->default(1);

            $table->unsignedBigInteger('invoice_handler_id')->nullable();

            $table->unsignedBigInteger('service_handler_type_id')->nullable();
            $table->unsignedBigInteger('service_handler_id')->nullable();
            
            $table->string('booking_possible_for')->nullable();
            $table->string('booking_from_id')->nullable();

            $table->integer('number_of_players')->nullable();
            $table->text('notes')->nullable();

            $table->unsignedBigInteger('configure_players_with_tee_times')->nullable();

            $table->unsignedBigInteger('status_id')->nullable();
            
            $table->timestamps();

            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('request_destination_id')->references('id')->on('request_destinations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('product_id')->references('id')->on('products')->onUpdate('cascade')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_products');
    }
}
