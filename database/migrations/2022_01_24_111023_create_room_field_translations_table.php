<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomFieldTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_field_translations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('room_field_id')->nullable();
            $table->unsignedBigInteger('language_id');
            
            $table->string('locale')->index();

            $table->longText('description')->nullable();

            $table->foreign('room_field_id')->references('id')->on('room_fields')->onUpdate('cascade')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('room_field_translations');
    }
}
