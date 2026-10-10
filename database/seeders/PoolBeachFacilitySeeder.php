<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facility;
use App\Models\Hotel;

class PoolBeachFacilitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    { 
        $pool = Facility::create([
            'name' => 'Pools',
            'type' => 'Hotel',
            'status' => '1',
        ]);
        $beach = Facility::create([
            'name' => 'Beach front',
            'type' => 'Hotel',
            'status' => '1',
        ]);

        $hotels = Hotel::get();

        foreach($hotels as $hotel)
        {
            $number = random_int ( 1 , 10 );
            $hotel->facilities()->attach([$pool->id => ['number' => $number]]);
            $hotel->facilities()->attach([$beach->id => ['number' => $number]]);
        }
        
    }
}
