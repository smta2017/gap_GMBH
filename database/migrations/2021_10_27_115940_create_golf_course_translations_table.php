<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateGolfCourseTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('golf_course_translations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('golf_course_id');
            $table->unsignedBigInteger('language_id');

            $table->string('locale')->index();

            $table->string('name')->nullable();
            $table->longText('website_description')->nullable();
            $table->longText('internal_description')->nullable();

            $table->unique(['golf_course_id', 'locale']);
            $table->foreign('golf_course_id')->references('id')->on('golf_courses')->onUpdate('cascade')->onDelete('cascade');
            $table->foreign('language_id')->references('id')->on('languages')->onUpdate('cascade')->onDelete('restrict');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('golf_course_translations');
    }
}
