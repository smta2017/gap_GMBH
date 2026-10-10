<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Company;
use App\Models\TourOperator;
use App\Models\City;
use DB;

class WfsHotelSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(true)
        {
            $path = base_path().'/public/backend/seeders/wfs_hotel.json';
            $json = json_decode(file_get_contents($path), true);

            $region_path = base_path().'/public/backend/seeders/wfs_region.json';
            $regionJson = json_decode(file_get_contents($region_path), true);

            $agency_path = base_path().'/public/backend/seeders/wfs_agency.json';
            $agencyJson = json_decode(file_get_contents($agency_path), true);

            foreach($json as $hotelData)
            {
                if(isset($hotelData['data']))
                {
                    foreach($hotelData['data'] as $hotel)
                    {

                        if($hotel['ho_name'] == '*NON LISTED HOTEL')
                        {
                            continue;
                        }

                        $cityData = null;
                        $countryData = null;
                        $regionData = null;
                        
                        $cityLetterCode = null;

                        foreach($regionJson as $regionDataArr)
                        {
                            if(isset($regionDataArr['data']))
                            {
                                foreach($regionDataArr['data'] as $regionItem)
                                {
                                    if($regionItem['reg_id'] == $hotel['reg_id'])
                                    {                       
                                        $cityCheck = DB::table('cities')->where('name', $regionItem['reg_name'])->first();
                                        
                                        if($cityCheck)
                                        {
                                            $regionData = $cityCheck->region_id;
                                            $countryData = $cityCheck->country_id;
                                            $cityData = $cityCheck->id;

                                            $cityLetterCode = $cityCheck->code;
                                        }
                                    }
                                }
                            }
                        }
                        
                        $operatorCheck = TourOperator::where('ref_id', $hotel['ageg_id'])->first();

                        if(!$operatorCheck)
                        {
                            $operatorCheck = TourOperator::where('ref_id', 1)->first();
                        }
                        
                        if($hotel['ho_enabled'] == '1')
                        {
                            $active = 1;
                        }else{
                            $active = 0;
                        }

                        $hotelData = Hotel::create([
                            "name" => $hotel['ho_name'],
                            "ref_id" => $hotel['ho_catalog_id'],
                            "letter_code" => $cityLetterCode,
                            // "number_of_rooms",
                            "company_id"=> $operatorCheck->company_id,
                            "active" => $active,
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
                            "street" => $hotel['ho_city'] . ' ' . $hotel['ho_street'],
                            // "postal_code",
                            // "location_link",
                            // "latitude",
                            // "longitude",
                            "phone" => $hotel['ho_phone'],
                            "fax" => $hotel['ho_fax'],
                            "email" => $hotel['ho_email'],
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

                        if($operatorCheck)
                        {
                            $hotelData->touroperators()->save($operatorCheck);
                        }
                    }
                }
            }

            
        }
    }
}
