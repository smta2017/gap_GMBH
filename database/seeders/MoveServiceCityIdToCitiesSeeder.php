<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ProductService;
use App\Models\City;

class MoveServiceCityIdToCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $services = ProductService::get();

        foreach($services as $service)
        {
            $checkCityID = \DB::table('product_service_city')->where('product_service_id', $service->id)->where('city_id', $service->city_id)->first();

            if(!$checkCityID)
            {
                $city = City::where('id', $service->city_id)->first();

                if($city)
                {
                    $service->cities()->save($city);
                }
            }
        }
    }
}
