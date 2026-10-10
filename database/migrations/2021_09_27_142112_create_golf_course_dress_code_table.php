<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGolfCourseDressCodeTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('golf_course_dress_code', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('golf_course_id');
            $table->unsignedBigInteger('dress_code_id');

            $table->foreign('golf_course_id')->references('id')->on('golf_courses')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('dress_code_id')->references('id')->on('dress_codes')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('golf_course_dress_code');
    }
}
