<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestProductTeeTimesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_product_tee_times', function (Blueprint $table) {
            $table->id();
            $table->boolean('is_parent')->default(0)->nullable();
            $table->unsignedBigInteger('parent_id')->nullable();
            $table->unsignedBigInteger('request_product_id');
            $table->unsignedBigInteger('request_product_details_id')->nullable();
            $table->unsignedBigInteger('golf_course_id')->nullable();
            $table->unsignedBigInteger('request_player_id')->nullable();

            $table->date('date')->nullable();
            $table->time('time_from')->nullable();
            $table->time('time_to')->nullable();
            $table->time('pref_time')->nullable();
            $table->time('conf_time')->nullable();

            $table->string("type")->nullable();
            $table->unsignedBigInteger("tee_time_id")->nullable();
            $table->unsignedBigInteger("min_tee_time_id")->nullable();
            $table->unsignedBigInteger("max_tee_time_id")->nullable();

            $table->unsignedBigInteger('status_id')->nullable();

            $table->timestamps();

            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('request_product_id')->references('id')->on('request_products')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('request_product_details_id')->references('id')->on('request_product_details')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('request_player_id')->references('id')->on('request_players')->onUpdate('cascade')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_product_tee_times');
    }
}
