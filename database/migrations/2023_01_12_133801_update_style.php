<?php

use App\Helper\Helpers;
use App\Models\GolfCourseStyle;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateStyle extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Helpers::upadte_GolfCourseStyle(1,'Desert',	'Wüste');
        // Helpers::upadte_GolfCourseStyle(2,'Heathland',	'Heidelandschaft');
        // Helpers::upadte_GolfCourseStyle(3,'Hillside',	'Berghang');
        // Helpers::upadte_GolfCourseStyle(4,'Inland',	'Inland');
        // Helpers::upadte_GolfCourseStyle(5,'Links',	'Links');
        // Helpers::upadte_GolfCourseStyle(6,'Mountain',	'Gebirge');
        // Helpers::upadte_GolfCourseStyle(7,'Parkland',	'Parklandschaft');
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
