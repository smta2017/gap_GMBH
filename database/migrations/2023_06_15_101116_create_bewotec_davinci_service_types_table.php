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
        Schema::create('bewotec_davinci_service_types', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('service_type_code', 10);
            $table->string('service_type_name', 255);
            $table->unsignedSmallInteger('participants');
            $table->unsignedSmallInteger('adults');
            $table->decimal('price', 8, 2);
            $table->decimal('price_avg', 8, 2);
            $table->unsignedTinyInteger('price_booking_related');
            $table->string('currency', 3);
            $table->string('availability', 3);
            $table->string('availability_detailed', 28);
            $table->unsignedTinyInteger('occupation_minimum');
            $table->unsignedTinyInteger('occupation_maximum');
            $table->unsignedTinyInteger('adults_minimum')->nullable();
            $table->unsignedTinyInteger('adults_maximum')->nullable();
            $table->unsignedTinyInteger('childs_maximum')->nullable();
            $table->unsignedTinyInteger('babys_maximum')->nullable();
            $table->dateTime('sync_last')->default(\DB::raw('CURRENT_TIMESTAMP'))->nullable(false);
            $table->timestamps();

            $table->index('service_id');
            $table->index('service_type_code');
            $table->index('participants');
            $table->index('adults');
            $table->index('price');
            $table->index('price_avg');

            $table->foreign('service_id')
                ->references('id')
                ->on('bewotec_davinci_services')
                ->onDelete('cascade')
                ->onUpdate('restrict');
                
            // Add the unique key
            $table->unique(['service_id', 'service_type_code', 'participants', 'adults'], 'service_type_unique');
        });

      
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bewotec_davinci_service_types');
    }
};
