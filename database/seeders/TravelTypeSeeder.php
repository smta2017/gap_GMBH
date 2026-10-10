<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TravelType;

class TravelTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(TravelType::count() == 0)
        {
            TravelType::create([
                'name' => "Individual Requests"
            ]);
            TravelType::create([
                'name' => "Golf Packages"
            ]);
            TravelType::create([
                'name' => "Pro Travel"
            ]);
            TravelType::create([
                'name' => "Round trips"
            ]);
            TravelType::create([
                'name' => "Group travel"
            ]);
            TravelType::create([
                'name' => "Special request for non-covered hotels"
            ]);
            TravelType::create([
                'name' => "Rental Clubs"
            ]);
            TravelType::create([
                'name' => "any request for covered destinations"
            ]);
            TravelType::create([
                'name' => "tournaments trip"
            ]);
        }
    }
}
