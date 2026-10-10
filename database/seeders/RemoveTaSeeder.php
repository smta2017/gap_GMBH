<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TravelAgency;

class RemoveTaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // foreach(TravelAgency::latest()->skip(200)->get() as $ag)
        // {
        //     if($ag->requests->count() == 0)
        //     {
        //         $ag->forceDelete();
        //     }
        // }

        $deleteUs = TravelAgency::latest()->take(TravelAgency::count())->skip(500)->get()->each(function($row){ 
            if($row->requests->count() == 0)
            {
                $row->forceDelete();
            }
        });
    }
}
