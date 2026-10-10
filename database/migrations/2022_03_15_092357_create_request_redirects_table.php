<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestRedirectsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('request_redirects', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('request_id')->nullable();
            $table->unsignedBigInteger('request_product_id')->nullable();
            $table->unsignedBigInteger('request_tee_time_id')->nullable();

            $table->text('subject')->nullable();
            $table->longText('body')->nullable();

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
        Schema::dropIfExists('request_redirects');
    }
}
