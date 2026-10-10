<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TravelAgency;
use App\Models\TourOperator;
use App\Models\Company;
use DB;

class TravelAgencySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $path = base_path().'/public/backend/seeders/wfs_agency.json';
        $json = json_decode(file_get_contents($path), true);

        $region_path = base_path().'/public/backend/seeders/wfs_region.json';
        $regionJson = json_decode(file_get_contents($region_path), true);

        if(TravelAgency::count() == 0)
        {
            foreach($json as $agencyData)
            {
                if(isset($agencyData['data']))
                {
                    foreach($agencyData['data'] as $agency)
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
                                    if($regionItem['reg_id'] == $agency['reg_id'])
                                    {
                                        $cityCheck = DB::table('cities')->where('name', $regionItem['reg_name'])->first();

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
                            'name' => $agency['age_name'],      
                            // 'hotel_id',
                            // 'rank',
                            // 'contract',
                     
                            'company_type_id' => 2,
                    
                            'region_id' => $regionData,
                            'country_id' => $countryData,
                            'city_id' => $cityData,
                       
                            "street" => $agency['age_street'],
                            // 'postal_code',
                            "phone" => $agency['age_phone'],
                            "fax" => $agency['age_fax'],
                            "email" => $agency['age_email'],
                            // 'latitude',
                            // 'longitude',
                            // 'location_link',
                    
                            // 'instagram',
                            // 'twitter',
                            // 'facebook',
                            // 'linkedin',
                        ]);

                        $ag = TravelAgency::create([
                            "company_id" => $company->id,
                            // "is_parent",
                            "name" => $agency['age_name'],
                            "ref_id" => $agency['age_tui_id'],
                            // "has_gfp_request",
                            "active" => $agency['age_enabled'],
                    
                            // "delegate_name",
                            // "delegate_email",
                            // "delegate_mobile_number",
                            // "delegate_user_id",
                            // "assigned_user_id",
                    
                            "region_id" => $regionData,
                            "country_id" => $countryData,
                            "city_id" => $cityData,
                            "street" => $agency['age_street'],
                            // "postal_code",
                            "phone" => $agency['age_phone'],
                            "fax" => $agency['age_fax'],
                            "email" => $agency['age_email'],
                            // "website",
                        ]);


                        $oper = TourOperator::where('ref_id', $agency['ageg_id'])->first();
                        
                        if($oper)
                        {
                            $ag->touroperators()->save($oper);
                        }
                        
                    }
                }
            }
        }

        // $agencyCityCheck = TravelAgency::where('city_id', null)->count();

        // if($agencyCityCheck > 0)
        // {
        //     foreach($json as $agencyData)
        //     {
        //         if(isset($agencyData['data']))
        //         {$count=0;
        //             foreach($agencyData['data'] as $agency)
        //             {

        //                 $cityData = null;
        //                 $countryData = null;
        //                 $regionData = null;
                        
        //                 $cityCheck = DB::table('cities')->where('name', $agency['age_city'])->first();

                        
        //                 if($cityCheck)
        //                 {
        //                     $regionData = $cityCheck->region_id;
        //                     $countryData = $cityCheck->country_id;
        //                     $cityData = $cityCheck->id;
        //                 }else{
        //                     $count++;
        //                 }
                       
        //                 $ag = TravelAgency::where('name', $agency['age_name'])->first();

        //                 // if($ag)
        //                 // {
        //                 //     $ag->update([
        //                 //         "region_id" => $regionData,
        //                 //         "country_id" => $countryData,
        //                 //         "city_id" => $cityData,
        //                 //     ]);
        //                 // }
                        
        //             }

        //             dd($count);
        //         }
        //     }
        // }
    }
}
