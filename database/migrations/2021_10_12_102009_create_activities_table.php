<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activities', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            
            $table->integer('activitieable_id');
            $table->string('activitieable_type');
            
            $table->string('start_time')->nullable();
            $table->string('end_time')->nullable();
            $table->string('start_recur')->nullable();
            $table->string('end_recur')->nullable();

            $table->string('duration')->nullable();

            $table->string('days_of_week')->nullable();

            $table->boolean('is_recurring')->default(0)->nullable();

            $table->string('color')->nullable();

            $table->unsignedBigInteger('type_id');

            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('type_id')->references('id')->on('activity_types')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activities');
    }
}
