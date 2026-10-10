<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('name');

            $table->boolean('is_package')->default(0);

            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('golf_course_id')->nullable();

            $table->string('code')->nullable();
            $table->string('ref_code')->nullable();

            $table->unsignedBigInteger('tee_time_id')->nullable();
            $table->unsignedBigInteger('hole_id')->nullable();

            $table->date('validity_from')->nullable();
            $table->date('validity_to')->nullable();

            $table->boolean('junior')->nullable()->default(1);
            $table->boolean('multi_players_only')->nullable()->default(1);
            $table->boolean('buggy')->nullable()->default(1);

            $table->boolean('use_service_configurations')->nullable()->default(0);
            
            $table->unsignedBigInteger('invoice_handler_id')->nullable();

            $table->unsignedBigInteger('service_handler_type_id')->nullable();
            $table->unsignedBigInteger('service_handler_id')->nullable();

            $table->string('booking_possible_for')->nullable();
            $table->string('booking_from_id')->nullable();

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
        Schema::dropIfExists('products');
    }
}
