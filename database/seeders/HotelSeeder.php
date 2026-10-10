<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Company;
use App\Models\ProductService;
use App\Models\User;
use App\Models\GolfCourse;
use App\Models\Product;
use App\Models\City;
use App\Models\Language;
use App\Models\ProductDetails;
use App\Models\TourOperator;
use App\Models\Link;
use DB;

class HotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(Hotel::count() == 0)
        {
            $path = base_path().'/public/backend/seeders/eggheads_hotels.json';
            $json = json_decode(file_get_contents($path), true);

            $region_path = base_path().'/public/backend/seeders/eggheads_regionen.json';
            $regionJson = json_decode(file_get_contents($region_path), true);

            foreach($json as $hotelData)
            {
                if(isset($hotelData['data']))
                {
                    foreach($hotelData['data'] as $hotel)
                    {
                        $cityData = null;
                        $countryData = null;
                        $regionData = null;
                        
                        foreach($regionJson as $regionDataArr)
                        {
                            if(isset($regionDataArr['data']))
                            {
                                foreach($regionDataArr['data'] as $regionItem)
                                {
                                    if($regionItem['uuid'] == $hotel['region'])
                                    {
                                        $cityCheck = DB::table('cities')->where('name', $regionItem['name'])->first();

                                        if($cityCheck)
                                        {
                                            $regionData = $cityCheck->region_id;
                                            $countryData = $cityCheck->country_id;
                                            $cityData = $cityCheck->id;
                                        }
                                    }
                                }
                            }
                        }
            
                        $company = Company::create([
                            "name"=> $hotel['name'],
                            // "email",
                            // "delegate_name",
                            // "delegate_email",
                            // "delegate_mobile_number",
                            // "delegate_user_id",
                            // "assigned_user_id",
                            "company_type_id"=> 4,
                            'region_id' => $regionData,
                            'country_id' => $countryData,
                            'city_id' => $cityData,
                            "postal_code"=> null,
                            "street"=> null,
                            "latitude"=> null,
                            "longitude"=> null,
                            "location_link"=> null,
                            "instagram"=> null,
                            "twitter"=> null,
                            "facebook"=> null,
                            "linkedin"=> null
                        ]);
            
                        if(is_numeric($hotel['hot_sum_rooms']))
                        {
                            $rooms = $hotel['hot_sum_rooms'];
                        }else{
                            $rooms = null;
                        }
            
                        $hotelData = Hotel::create([
                            "name" => $hotel['name'],
                            "ref_id" => $hotel['uuid'],
                            // "letter_code",
                            "number_of_rooms" => $rooms,
                            "company_id"=> $company->id,
                            // "active",
                            // "direct_contract",
                            // "via_dmc",
                            // "is_company_address",
                            // "delegate_name",
                            // "delegate_email",
                            // "delegate_mobile_number",
                            // "delegate_user_id",
                            // "assigned_user_id",
            
                            'region_id' => $regionData,
                            'country_id' => $countryData,
                            'city_id' => $cityData,
                            // "street",
                            // "postal_code",
                            // "location_link",
                            "latitude" => $hotel['univ_latitude'],
                            "longitude"=> $hotel['univ_longitude'],
                            // "phone",
                            // "fax",
                            // "email",
                            // "website_description",
                            // "internal_description",
            
                            // "handler_type_id",
                            // "handler_id",
                            // "payee",
                            // "is_payee_only",
                            // "payee_key_created",
                            // "bank",
                            // "bank_location",
                            // "account_number",
                            // "swift_code",
                            // "iban",
                            // "reservation_email",
                            // "booking_accounting_id",
                            // "has_golf_course",
                            // "golf_desk",
                            // "golf_shuttle",
                            // "storage_room",
                        ]);
            
                        $jsonImages = $hotel['pictures']; 
                        $images = json_decode($jsonImages, true); 
                        foreach($images as $key=>$value)
                        { 
                            $image_name = $key ; 
                            $image_code = $value; 
                            // $image_name = convert_hotel_img_text_url($image_name); 
                            $the_img_url = $image_code . '-' . $image_name; 
                            
                            $hotelData->images()->create(['file_name' =>  $the_img_url]);
                        }
                    }
                }
            }
        }
        
        
        $hotels = Hotel::get();
        foreach($hotels as $hotel)
        {
            $city  = City::find($hotel->city_id);
            if($city)
            {
                Hotel::find($hotel->id)->update([
                    'letter_code' => $city->code,
                ]);
            }

            if($hotel->fields->count() == 0)
            {
                $field = $hotel->fields()->create([
                    "type_id" => 3,
                    "is_html" => "0",
                    "description" => $hotel->internal_description,
                ]);

                $language = Language::find(2);

                if($language)
                {
                    $field->translations()->create([
                        'language_id' => 2,
                        'locale' => $language->code,
                        'description' => $hotel->internal_description,
                    ]);
                }
            }

            if(date('Y-m-d', strtotime($hotel->created_at)) <= date('Y-m-d', strtotime('2022-01-19')))
            {
                $hotel->update([
                    'is_golf_globe' => '1'
                ]);
    
                $operator = TourOperator::find('22');
    
                if($operator)
                {
                    if($hotel->touroperators()->count() == 0)
                    {
                        $hotel->touroperators()->save($operator);
                    }
                    
                }
            }
        }
    }
}
