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
use DB;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $company = Company::where('company_type_id', '4')->first;

        // if($company)
        // {
        //     $hotel = Hotel::where('company_id', $company->id)->first();
        // }

        // if($company && $hotel && Product::count() == 0)
        // {
        //     $service = ProductService::create([
        //         "name"=> "Service1",
        //         "type"=> "Hotel",
        //         "company_type_id"=> $company->company_type_id,
        //         "company_id"=> $company->id,
        //         "provider_id"=> $hotel->id,
        //         "country_id"=> 64,
        //         "city_id"=> 342,
        //         "letter_code"=> "ht1",
        //         "validity_from"=> "2021-11-18",
        //         "validity_to"=> "2023-11-10",
        //         "code"=> "ht11",
        //         "ref_code"=> null,
        //         "invoice_handler_id"=> $company->id,
        //         "service_handler_type_id"=> $company->company_type_id,
        //         "service_handler_id"=> $company->id,
        //         "booking_possible_for"=> "Hotel",
        //         "booking_from_id"=> "1",
        //         "active"=> 1
        //     ]);


        //     Product::create([
        //         'name' => 'single pro 1',

        //         'is_package' => '0',
                
        //         'service_id' => $service->id,
        //         'golf_course_id' => GolfCourse::first()->id,
        
        //         'code' => 'IRE51',
        
        //         'tee_time_id' => '4',
        //         'hole_id' => '1',
        
        //         'validity_from' => '2021-12-06',
        //         'validity_to' => '2021-12-10',
        
        //         'junior' => '0',
        //         'multi_players_only' => '0',
        //         'buggy' => '0',
        
        //         'use_service_configurations' => '1',
        
        //         'invoice_handler_id' => $company->id,
        
        //         'service_handler_type_id' => $company->company_type_id,
        //         'service_handler_id' => $company->id,
        
        //         'booking_possible_for' => 'City',
        //     ]);

        //     Product::create([
        //         'name' => 'single pro 2',

        //         'is_package' => '0',
                
        //         'service_id' => $service->id,
        //         'golf_course_id' => GolfCourse::first()->id,
        
        //         'code' => 'IRE52',
        
        //         'tee_time_id' => '3',
        //         'hole_id' => '1',
        
        //         'validity_from' => '2021-12-10',
        //         'validity_to' => '2021-12-22',
        
        //         'junior' => '1',
        //         'buggy' => '1',
        
        //         'use_service_configurations' => '1',
        
        //         'invoice_handler_id' => $company->id,
        
        //         'service_handler_type_id' => $company->company_type_id,
        //         'service_handler_id' => $company->id,
        
        //         "booking_possible_for"=> "City",
        //     ]);

        //     $p3 = Product::create([
        //         'name' => 'Golf Package 1',

        //         'is_package' => '1',
                
        //         'service_id' => $service->id,
        //         'golf_course_id' => GolfCourse::first()->id,
        
        //         'code' => 'IRE51',
        
        //         'tee_time_id' => '5',
        
        //         'validity_from' => '2021-12-06',
        //         'validity_to' => '2021-12-10',
        
        //         'junior' => '1',
        //         'multi_players_only' => '1',
        //         'buggy' => '1',
        
        //         'use_service_configurations' => '1',
        
        //         'invoice_handler_id' => $company->id,
        
        //         'service_handler_type_id' => $company->company_type_id,
        //         'service_handler_id' => $company->id,
        
        //         'booking_possible_for' => 'City',
        //     ]);

        //     ProductDetails::create([
        //         'product_id' => $p3->id,
        //         'golf_course_id' => GolfCourse::first()->id,
        //         'type' => 'Fixed',
        //         'tee_time_id' => '3',
        //     ]);
        //     ProductDetails::create([
        //         'product_id' => $p3->id,
        //         'golf_course_id' => GolfCourse::first()->id,
        //         'type' => 'Min-Max',
        //         'min_tee_time_id' => '1',
        //         'max_tee_time_id' => '3',
        //     ]);
        //     ProductDetails::create([
        //         'product_id' => $p3->id,
        //         'golf_course_id' => GolfCourse::first()->id,
        //         'type' => 'Min-Max',
        //         'min_tee_time_id' => '1',
        //         'max_tee_time_id' => '3',
        //     ]);
        // }


        // $products = DB::table('wfs_gfp')->where('gfp_enabled', '1')->get();


        // foreach($products as $item)
        // {

        //     $teeTime = TeeTime::where('name', $item->gfp_total_gf)->first();

        //     $city_id = null;
        //     $country_id = null;
        //     $letter_code = null;
            
        //     $cityWfs = DB::table('wfs_region')->where('reg_id', $item->reg_id)->first();

        //     if($cityWfs)
        //     {
        //         $cityCheck = City::where('name', 'LIKE', '%' .  $cityWfs->reg_name . '%')->first();

        //         if($cityCheck)
        //         {
        //             $city_id = $cityCheck->id;
        //             $country_id = $cityCheck->country_id;
        //             $letter_code = $cityCheck->code;
        //         }
        //     }
            
        //     if($item->ho_id != '0')
        //     {
        //         $service_handler_type_id = '4';
        //         $service_handler_id = null;

        //         $hotelWfs = DB::table('wfs_hotel')->where('ho_id', $item->ho_id)->first();

        //         if($hotelWfs)
        //         {
        //             $hotelCheck = Hotel::where('name', 'LIKE', '%' .  $hotelWfs->ho_name . '%')->first();
    
        //             if($hotelCheck)
        //             {
        //                 $service_handler_id = $hotelCheck->company_id;
        //             }
        //         }
        //     }else{
        //         $service_handler_type_id = '3';
        //         $service_handler_id = null;
        //     }

        //     if($item->gfp_redirect_ho_id != '0')
        //     {
        //         $booking_possible_for = 'Hotel';
        //         $booking_from_id = null;

        //         $rhotelWfs = DB::table('wfs_hotel')->where('ho_id', $item->gfp_redirect_ho_id)->first();

        //         if($rhotelWfs)
        //         {
        //             $rhotelCheck = Hotel::where('name', 'LIKE', '%' .  $rhotelWfs->ho_name . '%')->first();
    
        //             if($rhotelCheck)
        //             {
        //                 $booking_from_id = $rhotelCheck->id;
        //             }
        //         }
        //     }else{
        //         $booking_possible_for = 'City';
        //         $booking_from_id = null;
        //     }



        //     $vFrom = \Carbon\Carbon::parse($item->gfp_valid_from)->format('Y-m-d');
        //     $vTo = \Carbon\Carbon::parse($item->gfp_valid_until)->format('Y-m-d');

        //     if($this->validateDate($vFrom))
        //     {
        //         $vFromInsert = $vFrom;
        //     }else{
        //         $vFromInsert = null;
        //     }

        //     if($this->validateDate($vTo))
        //     {
        //         $vToInsert = $vTo;
        //     }else{
        //         $vToInsert = null;
        //     }
            
        //     $service = ProductService::create([
        //         "name"=> $item->gfp_tui_id,
        //         "type"=> "GOLF",
        //         "company_type_id"=> '1',
        //         "company_id"=> '1',

        //         "country_id"=> $country_id,
        //         "city_id"=> $city_id,
        //         "letter_code"=> $letter_code,
                
        //         'validity_from' => $vFromInsert,
        //         'validity_to' => $vToInsert,

        //         "invoice_handler_id"=> '1',

        //         "service_handler_type_id" => $service_handler_type_id,
        //         "service_handler_id"=> $service_handler_id,

        //         "booking_possible_for" => $booking_possible_for,
        //         "booking_from_id" => $booking_from_id,

        //         "active"=> 1
        //     ]);

        //     $p3 = Product::create([
        //         'name' => $item->gfp_name,

        //         'is_package' => '1',
                
        //         'service_id' => $service->id,

        //         'ref_code' => $item->gfp_tui_id,
                
        //         'tee_time_id' => $teeTime->id,
        //         'hole_id' => '2',
        
        //         'validity_from' => $vFromInsert,
        //         'validity_to' => $vToInsert,
                
        //         'invoice_handler_id' => '1',
        
        //         "service_handler_type_id" => $service_handler_type_id,
        //         "service_handler_id"=> $service_handler_id,
        
        //         "booking_possible_for" => $booking_possible_for,
        //         "booking_from_id" => $booking_from_id,
        //     ]);

        //     $details = DB::table('wfs_gfp_detail')->where('gfp_id', $item->gfp_id)->get();

        //     foreach($details as $detail)
        //     {

        //         $golf_course_id = null;

        //         $golfWfs = DB::table('wfs_golf_course')->where('gc_id', $detail->gc_id)->first();

        //         if($golfWfs)
        //         {
        //             $golfCheck = GolfCourse::where('name', 'LIKE', '%' .  $golfWfs->gc_name . '%')->first();
    
        //             if($golfCheck)
        //             {
        //                 $golf_course_id = $golfCheck->id;
        //             }
        //         }

        //         if($detail->gfpd_type == '1')
        //         {
        //             $type = 'Fixed';

        //             $tee_time = $detail->gfpd_gf_fix;

        //             $tee_time_id = TeeTime::where('name', $tee_time)->first()->id;

        //             $min_tee_time_id = null;
        //             $max_tee_time_id = null;

        //         }else{
        //             $type = 'Min-Max';

        //             $tee_time_id = null;

        //             $min_tee_time = $detail->gfpd_gf_min;
        //             $min_tee_time_id = TeeTime::where('name', $min_tee_time)->first()->id;
                    
        //             $max_tee_time = $detail->gfpd_gf_max;
        //             $max_tee_time_id = TeeTime::where('name', $max_tee_time)->first()->id;
        //         }

        //         ProductDetails::create([
        //             'product_id' => $p3->id,
        //             'golf_course_id' => $golf_course_id,
        //             'type' => $type,
        //             'tee_time_id' => $tee_time_id,
        //             'min_tee_time_id' => $min_tee_time_id,
        //             'max_tee_time_id' => $max_tee_time_id,
        //         ]); 
        //     }

        // }


        $service_path = base_path().'/public/backend/seeders/product_services.json';
        $services = json_decode(file_get_contents($service_path), true);

        $products_path = base_path().'/public/backend/seeders/products.json';
        $products = json_decode(file_get_contents($products_path), true);

        $details_path = base_path().'/public/backend/seeders/product_details.json';
        $details = json_decode(file_get_contents($details_path), true);

        if(ProductService::count() == 0)
        {
            foreach($services as $serviceData)
            {
                if(isset($serviceData['data']))
                {
                    foreach($serviceData['data'] as $service)
                    {
                        ProductService::create([
                            'name' => $service['name'],
                            'type' => $service['type'],
                    
                            'company_type_id' => $service['company_type_id'],
                            
                            'company_id' => $service['company_id'],
                    
                            'provider_id' => $service['provider_id'],
                    
                            'country_id' => $service['country_id'],
                            'city_id' => $service['city_id'],
                    
                            'letter_code' => $service['letter_code'],
                    
                            'code' => $service['code'],
                            'ref_code' => $service['ref_code'],
                    
                            'validity_from' => $service['validity_from'],
                            'validity_to' => $service['validity_to'],
                    
                            'invoice_handler_id' => $service['invoice_handler_id'],
                    
                            'service_handler_type_id' => $service['service_handler_type_id'],
                            'service_handler_id' => $service['service_handler_id'],
                    
                            'booking_possible_for' => $service['booking_possible_for'],
                            'booking_from_id' => $service['booking_from_id'],
                        ]);
                    }
                }
            }
        }

        if(Product::count() == 0)
        {
            foreach($products as $productData)
            {
                if(isset($productData['data']))
                {
                    foreach($productData['data'] as $product)
                    {
                        Product::create([
                            'name' => $product['name'],

                            'is_package' => $product['is_package'],
                            
                            'service_id' => $product['service_id'],
                            'golf_course_id' => $product['golf_course_id'],
                    
                            'code' => $product['code'],
                            'ref_code' => $product['ref_code'],
                    
                            'tee_time_id' => $product['tee_time_id'],
                            'hole_id' => $product['hole_id'],
                    
                            'validity_from' => $product['validity_from'],
                            'validity_to' => $product['validity_from'],
                    
                            'junior' => $product['junior'],
                            'multi_players_only' => $product['multi_players_only'],
                            'buggy' => $product['buggy'],
                    
                            'use_service_configurations' => $product['use_service_configurations'],
                    
                            'invoice_handler_id' => $product['invoice_handler_id'],
                    
                            'service_handler_type_id' => $product['service_handler_type_id'],
                            'service_handler_id' => $product['service_handler_id'],
                    
                            'booking_possible_for' => $product['booking_possible_for'],
                            'booking_from_id' => $product['booking_from_id'],
                        ]);
                    }
                }
            }
        }

        if(ProductDetails::count() == 0)
        {
            foreach($details as $detailData)
            {
                if(isset($detailData['data']))
                {
                    foreach($detailData['data'] as $detail)
                    {
                        ProductDetails::create([
                            'product_id' => $detail['product_id'],
                            'golf_course_id' => $detail['golf_course_id'],
                            'type' => $detail['type'],
                            'tee_time_id' => $detail['tee_time_id'],
                            'min_tee_time_id' => $detail['min_tee_time_id'],
                            'max_tee_time_id' => $detail['max_tee_time_id'],
                        ]);
                    }
                }
            }
        }

        foreach(ProductService::get() as $service)
        {
            if($service->booking_possible_for == 'Hotel' && $service->booking_from_id == null)
            {
                foreach($service->products as $product)
                {
                    foreach($product->details as $detail)
                    {
                        $detail->forceDelete();
                    }
                    $product->forceDelete();
                }
                $service->forceDelete();
            }
        }

        foreach(ProductDetails::get() as $details)
        {
            if($details->golf_course_id == null)
            {
                $details->forceDelete();
            }
        }
        foreach(Product::get() as $product)
        {
            if($product->booking_possible_for == 'Hotel' && $product->booking_from_id == null)
            {
                foreach($product->details as $detail)
                {
                    $detail->forceDelete();
                }
                $product->forceDelete();
                continue;
            }

            if($product->details->count() == 0)
            {
                $product->forceDelete();
            }
        }

    }

    function validateDate($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        // The Y ( 4 digits year ) returns TRUE for any integer with any number of digits so changing the comparison from == to === fixes the issue.
        return $d && $d->format($format) === $date;
    }
}
