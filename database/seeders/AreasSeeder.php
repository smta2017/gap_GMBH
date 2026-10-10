<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;
use App\Models\Area;
use App\Models\Language;
use DB;

class AreasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $path = base_path().'/public/backend/seeders/areasSeeder.json';
        $json = json_decode(file_get_contents($path), true);

        foreach($json as $item)
        {

            if($item['COUNTRY_3letters_CODE'] != '')
            {
                $countryCheck = Country::where('code', $item['COUNTRY_3letters_CODE'])->first();
                if($countryCheck)
                {
                    if($countryCheck->translations()->count() == 0)
                    {
                        $countryCheck->update([
                            'name' => $item['COUNTRY_NAME_in_DE']
                        ]);
    
                        $countryCheck->translations()->create([
                            'language_id' => '1',
                            'locale' => 'en',
                            'name' => $item['COUNTRY_NAME_in_EN'],    
                        ]);
            
                        $countryCheck->translations()->create([
                            'language_id' => '2',
                            'locale' => 'de',
                            'name' => $item['COUNTRY_NAME_in_DE'],    
                        ]);
                    }
                }
            }
            if($item['REGION_CODE'] != '')
            {
                $cityCheck = City::where('code', $item['REGION_CODE'])->first();
                if($cityCheck)
                {
                    if($cityCheck->translations()->count() == 0)
                    {
                        $cityCheck->update([
                            'name' => $item['REGION_NAME in DE']
                        ]);
    
                        $cityCheck->translations()->create([
                            'language_id' => '1',
                            'locale' => 'en',
                            'name' => $item['REGION_NAME in EN'],    
                        ]);
            
                        $cityCheck->translations()->create([
                            'language_id' => '2',
                            'locale' => 'de',
                            'name' => $item['REGION_NAME in DE'],    
                        ]);
                    }
                }
            }
            // if($item['Area in DE'] != '' && $item['Area in EN'] != '')
            // {
            //     $cityCheck = City::where('code', $item['REGION_CODE'])->first();
            //     if($cityCheck)
            //     {
            //         // insert area
            //         $area = Area::create([
            //             'name' => $item['Area in DE'],
            //             'region_id' => $cityCheck->region_id,
            //             'country_id' => $cityCheck->country_id,
            //             'city_id' => $cityCheck->id,
            //             'language_id' => '2'
            //         ]);

            //         if($area->translations()->count() == 0)
            //         {    
            //             $area->translations()->create([
            //                 'language_id' => '1',
            //                 'locale' => 'en',
            //                 'name' => $item['Area in EN'],    
            //             ]);
            
            //             $area->translations()->create([
            //                 'language_id' => '2',
            //                 'locale' => 'de',
            //                 'name' => $item['Area in DE'],    
            //             ]);
            //         }
            //     }
            // }
        }
    
    }
}
