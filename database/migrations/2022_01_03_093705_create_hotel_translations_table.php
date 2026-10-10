<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHotelTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('hotel_translations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('hotel_id');
            $table->unsignedBigInteger('language_id');

            $table->string('locale')->index();

            $table->string('name')->nullable();
            $table->longText('website_description')->nullable();
            $table->longText('internal_description')->nullable();

            $table->unique(['hotel_id', 'locale']);
            $table->foreign('hotel_id')->references('id')->on('hotels')->onUpdate('cascade')->onDelete('cascade');
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
        Schema::dropIfExists('hotel_translations');
    }
}
