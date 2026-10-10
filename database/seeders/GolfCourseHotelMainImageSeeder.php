<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GolfCourse;
use App\Models\Hotel;
use App\Models\Image;

class GolfCourseHotelMainImageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $golfcourses = GolfCourse::get();

        foreach($golfcourses as $course)
        {
            if($course->images->count() > 0)
            {
                $foundMainImage = false;
                foreach($course->images as $imageItem)
                {
                    if($imageItem->is_main == '1')
                    {
                        $foundMainImage = true;
                    }
                }
    
                $image = $course->images->first();
                if($image && $foundMainImage == false)
                {
                    $image->update([
                        'is_main' => '1'
                    ]);
                }
            }
        }

        $hotels = Hotel::get();

        foreach($hotels as $hotel)
        {
            if($hotel->images->count() > 0)
            {
                $foundMainImage = false;
                foreach($hotel->images as $imageItem)
                {
                    if($imageItem->is_main == '1')
                    {
                        $foundMainImage = true;
                    }
                }
    
                $image = $hotel->images->first();
                if($image && $foundMainImage == false)
                {
                    $image->update([
                        'is_main' => '1'
                    ]);
                }
            }
        }
    }
}
