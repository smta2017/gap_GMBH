<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFieldTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('field_translations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('field_id')->nullable();
            $table->unsignedBigInteger('language_id');
            
            $table->string('locale')->index();

            $table->longText('description')->nullable();

            $table->foreign('field_id')->references('id')->on('fields')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('field_translations');
    }
}
