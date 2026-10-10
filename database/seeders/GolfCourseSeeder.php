<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GolfCourse;
use App\Models\Company;
use App\Models\City;
use App\Models\Hotel;
use App\Models\Language;
use DB;

class GolfCourseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(GolfCourse::count() == 0)
        {
            $path = base_path().'/public/backend/seeders/eggheads_golfplaetze.json';
            $json = json_decode(file_get_contents($path), true);

            $clubs_path = base_path().'/public/backend/seeders/clubs-courses.json';
            $clubs = json_decode(file_get_contents($clubs_path), true);

            $region_path = base_path().'/public/backend/seeders/eggheads_regionen.json';
            $regionJson = json_decode(file_get_contents($region_path), true);

            foreach($json as $golfcourseData)
            {
                if(isset($golfcourseData['data']))
                {
                    foreach($golfcourseData['data'] as $golfcourse)
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
                                    if($regionItem['uuid'] == $golfcourse['region'])
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
                        
                        $hotel_id = null;

                        foreach($clubs as $club)
                        {
                            if( $club['Ref id'] == $golfcourse['uuid'])
                            {
                                $company = Company::where('name', $club['Golf Club'])->where('company_type_id', '3')->first();

                                if(!$company)
                                {

                                    $hotelCheck = Hotel::where('ref_id', $club['Hotel -id'])->first();

                                    if($hotelCheck)
                                    {
                                        $hotel_id = $hotelCheck->id;
                                    }

                                    $company = Company::create([
                                        'name' => $club['Golf Club'],      
                                        'hotel_id' => $hotel_id,
                                        // 'rank',
                                        // 'contract',
                                 
                                        'company_type_id' => 3,
                                
                                        'region_id' => $regionData,
                                        'country_id' => $countryData,
                                        'city_id' => $cityData,
                                        // 'postal_code',
                                        // 'street',
                                        'latitude' => $golfcourse['univ_latitude'],
                                        'longitude' => $golfcourse['univ_longitude'],
                                        // 'location_link',
                                
                                        // 'instagram',
                                        // 'twitter',
                                        // 'facebook',
                                        // 'linkedin',
                                    ]);
                                }
                            }
                        }

                        if(!$company)
                        {
                            // dd($golfcourse['uuid']);
                            $company_id = null;
                        }else{
                            $company_id = $company->id;
                        }

                        // $company = Company::create([
                        //     'name' => $golfcourse['name'],            
                        //     // 'rank',
                        //     // 'contract',
                     
                        //     'company_type_id' => 3,
                    
                        //     'region_id' => $regionData,
                        //     'country_id' => $countryData,
                        //     'city_id' => $cityData,
                        //     // 'postal_code',
                        //     // 'street',
                        //     'latitude' => $golfcourse['univ_latitude'],
                        //     'longitude' => $golfcourse['univ_longitude'],
                        //     // 'location_link',
                    
                        //     // 'instagram',
                        //     // 'twitter',
                        //     // 'facebook',
                        //     // 'linkedin',
                        // ]);
        
                        if(is_numeric($golfcourse['gco_sum_wholes']))
                        {
                            $hole = $golfcourse['gco_sum_wholes'];
                        }else{
                            $hole = null;
                        }
        
                        $course = GolfCourse::create([
                            "company_id" => $company_id,
                            'hotel_id' => $hotel_id,
                            "name" => $golfcourse['name'],
                            "ref_id" => $golfcourse['uuid'],
                            // "letter_code",
                            // "golf_course_style_id",
                            "website_description" => strip_tags($golfcourse['univ_description']),
                            "internal_description" => strip_tags($golfcourse['univ_description']),
                            // "designer",
                            // "active",
                            // "direct_contract",
                            // "via_dmc",
                            // "via_hotel",
                            // "handler_type_id",
                            // "handler_id",
                            "length_men" => (int) filter_var($golfcourse['gco_length_male_m'], FILTER_SANITIZE_NUMBER_INT),
                            "length_women" => (int) filter_var($golfcourse['gcoe_length_female_m'], FILTER_SANITIZE_NUMBER_INT),
                            "par_men" => (int) filter_var($golfcourse['gco_rating_par_male'], FILTER_SANITIZE_NUMBER_INT),
                            "par_women" => (int) filter_var($golfcourse['gco_rating_par_female'], FILTER_SANITIZE_NUMBER_INT),
                            "holes" => $hole,
                            // "course_rating",
                            // "club_rating",
                            // "slope_from",
                            // "slope_to",
                            // "academy",
                            // "pros",
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
                            'latitude' => $golfcourse['univ_latitude'],
                            'longitude' => $golfcourse['univ_longitude'],
                            // "phone",
                            // "fax",
                            // "email",
                            // "payee",
                            // "is_payee_only",
                            // "payee_key_created",
                            // "bank",
                            // "bank_location",
                            // "account_number",
                            // "swift_code",
                            // "iban",
                    
                            // "start_frequency",
                            // "start_gift",
                    
                            // "membership",
                            // "hcp_men",
                            // "hcp_women",
                            // "created_by",
                            // "updated_by",
                            // "deleted_by"
                        ]);
        
                        $json = $golfcourse['pictures']; 
                        $images = json_decode($json, true); 
                        foreach($images as $key=>$value)
                        { 
                            $image_name = $key ; 
                            $image_code = $value; 
                            // $image_name = convert_hotel_img_text_url($image_name); 
                            $the_img_url = $image_code . '-' . $image_name; 
                            
                            $course->images()->create(['file_name' =>  $the_img_url]);
                        }
                    }
                }
            }
        }

        $golfcourses = GolfCourse::get();
        foreach($golfcourses as $golfcourse)
        {
            $city  = City::find($golfcourse->city_id);
            if($city)
            {
                GolfCourse::find($golfcourse->id)->update([
                    'letter_code' => $city->code,
                ]);
            }

            if($golfcourse->fields->count() == 0)
            {
                $field = $golfcourse->fields()->create([
                    "type_id" => 3,
                    "is_html" => "0",
                    "description" => $golfcourse->internal_description,
                ]);

                $language = Language::find(2);

                if($language)
                {
                    $field->translations()->create([
                        'language_id' => 2,
                        'locale' => $language->code,
                        'description' => $golfcourse->internal_description,
                    ]);
                }
            }
        }
    }
}
