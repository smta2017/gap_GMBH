<?php

namespace Database\Seeders;

use App\Helper\Helpers;
use Illuminate\Database\Seeder;
use App\Models\GolfCourseStyle;

class GolfCourseStyleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(GolfCourseStyle::count() == 0)
        {
            Helpers::create_GolfCourseStyle('Desert','	Wüste');
            Helpers::create_GolfCourseStyle('Heathland',' Heidelandschaft');
            Helpers::create_GolfCourseStyle('Hillside',' Berghang');
            Helpers::create_GolfCourseStyle('Inland',' Inland');
            Helpers::create_GolfCourseStyle('Links',' Links');
            Helpers::create_GolfCourseStyle('Mountain',' Gebirge');
            Helpers::create_GolfCourseStyle('Parkland',' Parklandschaft');
        }
    }
}
