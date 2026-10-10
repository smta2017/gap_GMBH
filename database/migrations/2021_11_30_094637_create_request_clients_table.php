<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestClientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_clients', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('request_id');

            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('booking_code')->nullable();
            $table->string('groups')->nullable();

            $table->boolean('is_leader')->nullable()->default(0);
            $table->unsignedBigInteger('leader_type_id')->nullable();
            $table->unsignedBigInteger('leader_company_id')->nullable();

            $table->timestamps();

            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('request_id')->references('id')->on('requests')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('request_clients');
    }
}
