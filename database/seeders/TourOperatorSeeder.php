<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourOperator;
use App\Models\Company;
use DB;

class TourOperatorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $path = base_path().'/public/backend/seeders/wfs_agency_group.json';
        $json = json_decode(file_get_contents($path), true);

        $region_path = base_path().'/public/backend/seeders/wfs_region.json';
        $regionJson = json_decode(file_get_contents($region_path), true);

        $country_path = base_path().'/public/backend/seeders/wfs_country_iso3166.json';
        $countryJson = json_decode(file_get_contents($country_path), true);

        
        if(TourOperator::count() == 0)
        {
            foreach($json as $operatorData)
            {
                if(isset($operatorData['data']))
                {
                    foreach($operatorData['data'] as $operator)
                    {

                        $cityData = null;
                        $countryData = null;
                        $regionData = null;
                        
                        // foreach($regionJson as $regionDataArr)
                        // {
                        //     if(isset($regionDataArr['data']))
                        //     {
                        //         foreach($regionDataArr['data'] as $regionItem)
                        //         {
                        //             if($regionItem['reg_id'] == $agency['reg_id'])
                        //             {
                        //                 $cityCheck = DB::table('cities')->where('name', $regionItem['reg_name'])->first();

                        //                 if($cityCheck)
                        //                 {
                        //                     $regionData = $cityCheck->region_id;
                        //                     $countryData = $cityCheck->country_id;
                        //                     $cityData = $cityCheck->id;
                        //                 }
                        //             }
                        //         }
                        //     }
                        // }
                        

                        $company = Company::create([
                            'name' => $operator['ageg_name'],      
                            // 'hotel_id',
                            // 'rank',
                            // 'contract',
                     
                            'company_type_id' => 5,
                    
                            'region_id' => $regionData,
                            'country_id' => $countryData,
                            'city_id' => $cityData,
                       
                            // "street",
                            // 'postal_code',
                            // "phone",
                            // "fax",
                            // "email",
                            // 'latitude',
                            // 'longitude',
                            // 'location_link',
                    
                            // 'instagram',
                            // 'twitter',
                            // 'facebook',
                            // 'linkedin',
                        ]);

                        $op = TourOperator::create([
                            "company_id" => $company->id,
                            // "is_parent",
                            "name" => $operator['ageg_name'],
                            "ref_id" => $operator['ageg_id'],
                            "has_gfp_requests" => $operator['ageg_gfp_support'],
                            "active" => $operator['ageg_enabled'],
                    
                            // "delegate_name",
                            // "delegate_email",
                            // "delegate_mobile_number",
                            // "delegate_user_id",
                            // "assigned_user_id",
                    
                            "region_id" => $regionData,
                            "country_id" => $countryData,
                            "city_id" => $cityData,
                            // "street",
                            // "postal_code",
                            // "phone",
                            // "fax",
                            // "email",
                            // "website",
                        ]);
                    }
                }
            }
        }

        $operatorCountryCheck = TourOperator::where('country_id', null)->count();

        if($operatorCountryCheck > 0)
        {
            foreach($json as $operatorData)
            {
                if(isset($operatorData['data']))
                {
                    foreach($operatorData['data'] as $operator)
                    {

                        $cityData = null;
                        $countryData = null;
                        $regionData = null;
                        
                        foreach($countryJson as $countryDataArr)
                        {
                            if(isset($countryDataArr['data']))
                            {
                                foreach($countryDataArr['data'] as $countryItem)
                                {
                                    if($countryItem['cou_id'] == $operator['cou_id'])
                                    {
                                        $countryCheck = DB::table('countries')->where('name', $countryItem['cou_name'])->first();

                                        if($countryCheck)
                                        {
                                            $regionData = $countryCheck->region_id;
                                            $countryData = $countryCheck->id;
                                        }
                                    }
                                }
                            }
                        }
                        

                        $op = TourOperator::where('ref_id', $operator['ageg_id'])->first();

                        if($op)
                        {
                            $op->update([
                                "region_id" => $regionData,
                                "country_id" => $countryData,
                                "city_id" => $cityData,
                            ]);
                        }
                    }
                }
            }
        }
    }
}
