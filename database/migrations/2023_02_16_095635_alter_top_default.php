<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AlterTopDefault extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('golf_courses', function (Blueprint $table) {
            $table->integer('top')->nullable()->default(0)->change();
        });
        
        Schema::table('hotels', function (Blueprint $table) {
            $table->integer('top')->nullable()->default(0)->change();
        });

        Schema::table('countries', function (Blueprint $table) {
            $table->integer('top')->nullable()->default(0)->change();
        });

        Schema::table('cities', function (Blueprint $table) {
            $table->integer('top')->nullable()->default(0)->change();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
