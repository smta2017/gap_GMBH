<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateObjectServiceAddonsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('object_service_addons', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('child_id');
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('service_addon_id');
            
            $table->integer('qty')->nullable();
            $table->integer('fees')->nullable();

            $table->string('selected_option')->nullable();

            $table->string('notes')->nullable();

            $table->string('active')->default('1')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('object_service_addons');
    }
}
