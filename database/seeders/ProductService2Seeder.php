<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Company;
use App\Models\ProductService;
use App\Models\User;
use App\Models\GolfCourse;
use App\Models\TeeTime;
use App\Models\City;
use App\Models\Product;
use App\Models\ProductDetails;
use App\Models\HotelProduct;
use App\Models\GolfHoliday;
use App\Models\Request;
use DB;

class ProductService2Seeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $service_path = base_path().'/public/backend/seeders/product_services_2.json';
        $services = json_decode(file_get_contents($service_path), true);

        Request::query()->forceDelete();

        Product::query()->forceDelete();
        \DB::statement("ALTER TABLE products AUTO_INCREMENT =  1");

        HotelProduct::query()->forceDelete();
        \DB::statement("ALTER TABLE hotel_products AUTO_INCREMENT =  1");

        GolfHoliday::query()->forceDelete();
        \DB::statement("ALTER TABLE golf_holidays AUTO_INCREMENT =  1");

        ProductService::query()->forceDelete();
        \DB::statement("ALTER TABLE product_services AUTO_INCREMENT =  1");


        if(ProductService::count() == 0)
        {
            foreach($services as $service)
            {
                
                if($service['Service Name'] == "" || $service['Service Name'] == ",")
                {
                    continue;
                }
                $serviceCheck = ProductService::where('name', $service['Service Name'])->where('ref_code', $service['Service code'])->first();

                if($serviceCheck) { continue; }

                $company = Company::where('name', $service['Company'])->first();

                // if(!$company)
                // {
                //     echo $service['Company'] . "<br>";
                // }

                

                $serviceCities = explode(',', $service['City']);

                foreach($serviceCities as $serviceCity)
                {
                    $city = City::where('code', $serviceCity)->first();
                    if(!$city)
                    {
                        // dd($service['City']);
                        echo $service['City'] . " ";
                    }
                }


                // ProductService::create([
                //     'name' => $service['Service Name'],
            
                //     'company_type_id' => $company->company_type_id,
                    
                //     'company_id' => $company->id,
            
                //     // 'provider_id' => null,
            
                //     'country_id' => $city->country_id,
                //     'city_id' => $city->id,
            
                //     'letter_code' => $city->code,
            
                //     'code' => null,
                //     'ref_code' => $service['Service code'],
            
                //     'validity_from' => $service['Validity From'],
                //     'validity_to' => $service['Validity From'],
            
                //     'invoice_handler_id' => $service['invoice_handler_id'],
            
                //     'service_handler_type_id' => $service['service_handler_type_id'],
                //     'service_handler_id' => $service['service_handler_id'],
            
                //     'booking_possible_for' => $service['booking_possible_for'],
                //     'booking_from_id' => $service['booking_from_id'],
                // ]);
                
            }
        }
    }
}
