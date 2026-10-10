<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Country;
use App\Models\City;
use App\Models\Area;
use App\Models\Region;
use App\Models\Language;
use DB;

class RegionTrSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $regions = Region::get();

        foreach($regions as $r)
        {

                if($r->translations()->count() == 0)
                {
                    $r->translations()->create([
                        'language_id' => '1',
                        'locale' => 'en',
                        'name' => $r->name,    
                    ]);
        
                    $r->translations()->create([
                        'language_id' => '2',
                        'locale' => 'de',
                        'name' => $r->name,    
                    ]);
                }
            
        }
    
    }
}
