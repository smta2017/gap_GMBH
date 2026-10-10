<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use DB;

class HotelCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $path = base_path().'/public/backend/seeders/golfglobe_hotels_category.json';
        $json = json_decode(file_get_contents($path), true);

        foreach($json as $hotelData)
        {
            if($hotelData['uuid'] == '')
            {
                continue;
            }

            $hotelDB = Hotel::where('ref_id', $hotelData['uuid'])->first();

            if($hotelDB)
            {
                
                $cat = (int) $hotelData['hot_category'];

                $hotelDB->update([
                    'hotel_rating' => $cat
                ]);
            }
        }
        
    }
}
