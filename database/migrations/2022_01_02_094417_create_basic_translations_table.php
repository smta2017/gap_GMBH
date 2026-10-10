<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBasicTranslationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('basic_translations', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('basicable_id')->nullable();
            $table->string('basicable_type')->nullable();
            $table->unsignedBigInteger('language_id');
            
            $table->string('locale')->index();

            $table->string('name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('basic_translations');
    }
}
