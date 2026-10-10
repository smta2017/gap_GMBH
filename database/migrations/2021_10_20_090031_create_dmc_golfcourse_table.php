<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDmcGolfcourseTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dmc_golfcourse', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('dmc_id');
            $table->unsignedBigInteger('golf_course_id');

            $table->foreign('dmc_id')->references('id')->on('dmcs')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('golf_course_id')->references('id')->on('golf_courses')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('dmc_golfcourse');
    }
}
