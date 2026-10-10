<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAreaIdToCourses6 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dmcs', function (Blueprint $table) {
            $table->unsignedBigInteger('area_id')->nullable();
        });
        Schema::table('companies', function (Blueprint $table) {
            $table->unsignedBigInteger('area_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tour_operators', function (Blueprint $table) {
            //
        });
    }
}
