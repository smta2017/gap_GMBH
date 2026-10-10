<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bewotec_davinci_services', function (Blueprint $table) {
            $table->id();
            $table->string('booking_code', 17);
            $table->unsignedInteger('booking_code_id');
            $table->string('booking_code_name', 70);
            $table->string('requirement', 2);
            $table->unsignedTinyInteger('requirement_flag');
            $table->date('date_from');
            $table->date('date_to');
            $table->unsignedTinyInteger('duration');
            $table->string('catalog_code', 4);
            $table->string('catalog_name', 40);
            $table->string('destination_name', 40)->nullable();
            $table->string('destination_code', 6)->nullable();
            $table->string('standard_meal_code', 1)->nullable();
            $table->unsignedBigInteger('package_service_id')->nullable();
            $table->unsignedTinyInteger('package_order')->nullable();
            $table->unsignedTinyInteger('package_type_of_assignment')->nullable();
            $table->dateTime('sync_last')->default(\DB::raw('CURRENT_TIMESTAMP'))->nullable(false);
            $table->timestamps();

            $table->index('booking_code');
            $table->index('date_from');
            $table->index('date_to');
            $table->index('duration');
            $table->index('catalog_code');
            $table->index('destination_code');
            $table->index('standard_meal_code');
            $table->index('package_service_id');

            $table->foreign('package_service_id')
                ->references('id')
                ->on('bewotec_davinci_services')
                ->onDelete('cascade')
                ->onUpdate('restrict');
                
            // Add the unique key
            $table->unique(['booking_code', 'requirement', 'catalog_code', 'date_from', 'date_to', 'duration', 'package_service_id'], 'service_unique');
        
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bewotec_davinci_services');
    }
};
