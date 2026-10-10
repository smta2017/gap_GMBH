<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestProductDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_product_details', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('request_product_id')->nullable();
            $table->unsignedBigInteger('product_id')->nullable();
            $table->unsignedBigInteger('product_details_id')->nullable();
            $table->unsignedBigInteger('golf_course_id')->nullable();
            
            $table->string("type")->nullable();
            $table->unsignedBigInteger("tee_time_id")->nullable();
            $table->unsignedBigInteger("min_tee_time_id")->nullable();
            $table->unsignedBigInteger("max_tee_time_id")->nullable();

            $table->timestamps();

            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('request_product_id')->references('id')->on('request_products')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_product_details');
    }
}
