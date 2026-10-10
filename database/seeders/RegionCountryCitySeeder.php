<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;
use DB;

class RegionCountryCitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $path = base_path().'/public/backend/seeders/region-countries-cities.json';
        $json = json_decode(file_get_contents($path), true);
    
        $region = '';
        $country = '';
        $countryCode = '';
    

        foreach($json as $item)
        {
            if($item['Region'] != '')
            {
                $region = $item['Region'];
            }
    
            if($item['Country-code'] != '')
            {
                $countryCode = $item['Country-code'];
            }
    
            if($item['Country'] != '')
            {
                $country = $item['Country'];
            }
    
            if($item['3 letters-code'] != '')
            {
                $cityCheck = City::where('code', $item['3 letters-code'])->first();
    
                if(!$cityCheck)
                {
                    $countryCheck = Country::where('code', $countryCode)->first();
    
                    if($countryCheck)
                    {
                        City::create([
                            'name' => $item['City'],
                            'code' => $item['3 letters-code'],
                            'region_id' => $countryCheck->region_id,
                            'country_id' => $countryCheck->id,
                            'language_id' => '1',
                            'status' => '1'
                        ]);
                    }
                }else{
                    $cityCheck->update([
                        'name' => $item['City']
                    ]);
                }
            }
    
        }
    }
}
