<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEnquiriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->date('arrival_date');
            $table->integer('group_number');
            $table->integer('number_of_nights');
            $table->integer('number_of_rounds');
            
            $table->boolean('flight');
            $table->boolean('receive_offer');
            $table->boolean('is_schedule_datetime')->nullable()->default(0);
            $table->datetime('schedule_datetime')->nullable();

            $table->string('first_name');
            $table->string('last_name');
            $table->string('mobile_number');
            $table->string('email');

            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('integration_id')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->unsignedBigInteger('status_id')->nullable();

            $table->unsignedBigInteger('source_id')->nullable();
            $table->unsignedBigInteger('medium_id')->nullable();
            $table->string('compaign')->nullable();

            $table->string('tracking_code')->nullable();

            $table->string('target')->nullable();
            $table->string('airport_name')->nullable();

            $table->string('ip_address')->nullable();

            $table->timestamps();

            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('city_id')->references('id')->on('cities')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('integration_id')->references('id')->on('integrations')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('company_id')->references('id')->on('companies')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('status_id')->references('id')->on('enquiry_statuses')->onUpdate('cascade')->onDelete('cascade');

            $table->foreign('source_id')->references('id')->on('sources')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('medium_id')->references('id')->on('mediums')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('enquiries');
    }
}
