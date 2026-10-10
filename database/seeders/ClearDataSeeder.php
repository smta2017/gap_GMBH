<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Request;
use App\Models\Product;
use App\Models\ProductService;
use App\Models\Season;
use App\Models\User;
use App\Models\Region;
use App\Models\Country;
use App\Models\City;
use App\Models\Area;
use App\Models\GolfCourse;
use App\Models\Hotel;
use App\Models\DMC;
use App\Models\TravelAgency;
use App\Models\TourOperator;
use App\Models\Company;

class ClearDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Request::withTrashed()->forceDelete(); 
        ProductService::withTrashed()->forceDelete(); 
        
        Product::withTrashed()->forceDelete();

        Season::withTrashed()->forceDelete();
        User::withTrashed()->forceDelete();

        Area::withTrashed()->forceDelete();
        City::withTrashed()->forceDelete();
        Country::withTrashed()->forceDelete();
        Region::withTrashed()->forceDelete();
        
        

        GolfCourse::withTrashed()->forceDelete();
        Hotel::withTrashed()->forceDelete();
        DMC::withTrashed()->forceDelete();
        TravelAgency::withTrashed()->forceDelete();
        TourOperator::withTrashed()->forceDelete();
        Company::withTrashed()->forceDelete();

    }
}
