<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGolfHolidayTagTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('golf_holiday_tag', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('golf_holiday_id');
            $table->unsignedBigInteger('tag_id');

            $table->foreign('golf_holiday_id')->references('id')->on('golf_holidays')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('tag_id')->references('id')->on('tags')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('golf_holiday_tag');
    }
}
