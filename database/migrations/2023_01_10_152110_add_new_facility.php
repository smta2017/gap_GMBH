<?php

use App\Helper\Helpers;
use App\Models\Facility;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNewFacility extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {

        Helpers::create_facility('Club House','Club House' ,'Golf Course' ,'' ,'', 1);
        Helpers::create_facility('Snack Bar','Snack Bar' ,'Golf Course' ,'' ,'', 1);

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
