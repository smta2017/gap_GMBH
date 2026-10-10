<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\ServiceProperty;
use App\Models\ServiceAddon;
use App\Models\ServiceFeeDetails;
use App\Models\ClubBrand;

class HotelSurroundingsServiceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Service::where('type', 'Hotel-Surroundings')->count() == 0)
        {
            Service::create([
                'name' => 'Right on the Beach',
                'type' => 'Hotel-Surroundings',
                'view_type' => 'boolean',
            ]);
            Service::create([
                'name' => 'Suttle to the city',
                'type' => 'Hotel-Surroundings',
                'view_type' => 'boolean',
            ]);
            Service::create([
                'name' => 'Airport Shuttle',
                'type' => 'Hotel-Surroundings',
                'view_type' => 'boolean',
            ]);
            Service::create([
                'name' => 'Shuttle to old town',
                'type' => 'Hotel-Surroundings',
                'view_type' => 'boolean',
            ]);
        }
    }
}
