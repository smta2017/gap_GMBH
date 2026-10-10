<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\DMC;
use App\Models\Company;
use App\Models\City;
use DB;

class DmcSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        if(DMC::count() == 0)
        {

            $region1 = null;
            $country1 = null;
            $city1 = null;

            $city1DB = City::where('code', 'RAK')->first();
            if($city1DB)
            {
                $city1 = $city1DB->id;
                $country1 = $city1DB->country_id;
                $region1 = $city1DB->region_id;
            }

            $company1 = Company::create([
                'name' => 'Time Travel Morocco',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region1,
                'country_id' => $country1,
                'city_id' => $city1,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'golf@timetravelmorocco.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc1 = DMC::create([
                "company_id" => $company1->id,
                // "is_parent",
                'name' => 'Time Travel Morocco',

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region1,
                "country_id" => $country1,
                "city_id" => $city1,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => 'golf@timetravelmorocco.com',
                // "website",
        
                "reservation_email" => 'resa2@timetravelmorocco.com',
            ]);


            $region2 = null;
            $country2 = null;
            $city2 = null;

            $city2DB = City::where('code', 'FAO')->first();
            if($city2DB)
            {
                $city2 = $city2DB->id;
                $country2 = $city2DB->country_id;
                $region2 = $city2DB->region_id;
            }

            $company2 = Company::create([
                'name' => 'PAGs',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region2,
                'country_id' => $country2,
                'city_id' => $city2,
           
                // 'street',
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

            $dmc2 = DMC::create([
                "company_id" => $company2->id,
                // "is_parent",
                'name' => 'PAGs',

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region2,
                "country_id" => $country2,
                "city_id" => $city2,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                // "email",
                // "website",
        
                // "reservation_email",
            ]);


            $region3 = null;
            $country3 = null;
            $city3 = null;

            $city3DB = City::where('code', 'FNC')->first();
            if($city3DB)
            {
                $city3 = $city3DB->id;
                $country3 = $city3DB->country_id;
                $region3 = $city3DB->region_id;
            }

            $company3 = Company::create([
                'name' => 'Madeira Golf Island',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region3,
                'country_id' => $country3,
                'city_id' => $city3,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'marcio.ferreira@travelone.pt',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc3 = DMC::create([
                "company_id" => $company3->id,
                // "is_parent",
                'name' => $company3->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region3,
                "country_id" => $country3,
                "city_id" => $city3,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => 'marcio.ferreira@travelone.pt',
                // "website",
        
                "reservation_email" => 'marcio.ferreira@travelone.pt',
            ]);


            $region4 = null;
            $country4 = null;
            $city4 = null;

            $city4DB = City::where('code', 'XRY')->first();
            if($city4DB)
            {
                $city4 = $city4DB->id;
                $country4 = $city4DB->country_id;
                $region4 = $city4DB->region_id;
            }

            $company4 = Company::create([
                'name' => 'Global Hemisphere',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region4,
                'country_id' => $country4,
                'city_id' => $city4,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'globalhemisphere@gmail.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc4 = DMC::create([
                "company_id" => $company4->id,
                // "is_parent",
                'name' => $company4->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region4,
                "country_id" => $country4,
                "city_id" => $city4,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => 'globalhemisphere@gmail.com',
                // "website",
        
                // "reservation_email",
            ]);


            $region5 = null;
            $country5 = null;
            $city5 = null;

            $city5DB = City::where('code', 'PUJ')->first();
            if($city5DB)
            {
                $city5 = $city5DB->id;
                $country5 = $city5DB->country_id;
                $region5 = $city5DB->region_id;
            }

            $company5 = Company::create([
                'name' => 'Global Hemisphere',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region5,
                'country_id' => $country5,
                'city_id' => $city5,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'globalhemisphere@gmail.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc5 = DMC::create([
                "company_id" => $company5->id,
                // "is_parent",
                'name' => $company5->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region5,
                "country_id" => $country5,
                "city_id" => $city5,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => $company5->email,
                // "website",
        
                // "reservation_email",
            ]);


            $region6 = null;
            $country6 = null;
            $city6 = null;

            $city6DB = City::where('code', 'HKT')->first();
            if($city6DB)
            {
                $city6 = $city6DB->id;
                $country6 = $city6DB->country_id;
                $region6 = $city6DB->region_id;
            }

            $company6 = Company::create([
                'name' => 'GolfAsian',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region6,
                'country_id' => $country6,
                'city_id' => $city6,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'boyd@golfasian.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc6 = DMC::create([
                "company_id" => $company6->id,
                // "is_parent",
                'name' => $company6->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region6,
                "country_id" => $country6,
                "city_id" => $city6,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => $company6->email,
                // "website",
        
                "reservation_email" => 'operations-vietnam@golfasian.com',
            ]);


            $region7 = null;
            $country7 = null;
            $city7 = null;

            $city7DB = City::where('code', 'BKK')->first();
            if($city7DB)
            {
                $city7 = $city7DB->id;
                $country7 = $city7DB->country_id;
                $region7 = $city7DB->region_id;
            }

            $company7 = Company::create([
                'name' => 'WesternTours',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region7,
                'country_id' => $country7,
                'city_id' => $city7,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'wtt@westerntourshuahin.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc7 = DMC::create([
                "company_id" => $company7->id,
                // "is_parent",
                'name' => $company7->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region7,
                "country_id" => $country7,
                "city_id" => $city7,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => $company7->email,
                // "website",
        
                "reservation_email" => 'wtt@westerntourshuahin.com',
            ]);


            $region8 = null;
            $country8 = null;
            $city8 = null;

            $city8DB = City::where('code', 'AUH')->first();
            if($city8DB)
            {
                $city8 = $city8DB->id;
                $country8 = $city8DB->country_id;
                $region8 = $city8DB->region_id;
            }

            $company8 = Company::create([
                'name' => 'Troon',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region8,
                'country_id' => $country8,
                'city_id' => $city8,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'jboniao@aldargolf.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc8 = DMC::create([
                "company_id" => $company8->id,
                // "is_parent",
                'name' => $company8->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region8,
                "country_id" => $country8,
                "city_id" => $city8,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => $company8->email,
                // "website",
        
                "reservation_email" => 'reservations@aldarleisure.com',
            ]);


            $region9 = null;
            $country9 = null;
            $city9 = null;

            $city9DB = City::where('code', 'DXB')->first();
            if($city9DB)
            {
                $city9 = $city9DB->id;
                $country9 = $city9DB->country_id;
                $region9 = $city9DB->region_id;
            }

            $company9 = Company::create([
                'name' => 'Travco',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region9,
                'country_id' => $country9,
                'city_id' => $city9,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'lankesh.f@travco.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc9 = DMC::create([
                "company_id" => $company9->id,
                // "is_parent",
                'name' => $company9->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region9,
                "country_id" => $country9,
                "city_id" => $city9,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => $company9->email,
                // "website",
        
                "reservation_email" => 'lankesh.f@travco.com',
            ]);


            $region10 = null;
            $country10 = null;
            $city10 = null;

            $city10DB = City::where('code', 'DXB')->first();
            if($city10DB)
            {
                $city10 = $city10DB->id;
                $country10 = $city10DB->country_id;
                $region10 = $city10DB->region_id;
            }

            $company10 = Company::create([
                'name' => 'DubaiGolf',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region10,
                'country_id' => $country10,
                'city_id' => $city10,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'pgonsalvez@dubaigolf.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc10 = DMC::create([
                "company_id" => $company10->id,
                // "is_parent",
                'name' => $company10->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region10,
                "country_id" => $country10,
                "city_id" => $city10,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => $company10->email,
                // "website",
        
                "reservation_email" => 'golfbooking@dubaigolf.com',
            ]);


            $region11 = null;
            $country11 = null;
            $city11 = null;

            $city11DB = City::where('code', 'NBE')->first();
            if($city11DB)
            {
                $city11 = $city11DB->id;
                $country11 = $city11DB->country_id;
                $region11 = $city11DB->region_id;
            }

            $company11 = Company::create([
                'name' => 'Sté Golf Protec Service',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region11,
                'country_id' => $country11,
                'city_id' => $city11,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'manager@golfprotec.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc11 = DMC::create([
                "company_id" => $company11->id,
                // "is_parent",
                'name' => $company11->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region11,
                "country_id" => $country11,
                "city_id" => $city11,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => $company11->email,
                // "website",
        
                "reservation_email" => 'golf.protec.service@gmail.com',
            ]);


            $region12 = null;
            $country12 = null;
            $city12 = null;

            $city12DB = City::where('code', 'AYT')->first();
            if($city12DB)
            {
                $city12 = $city12DB->id;
                $country12 = $city12DB->country_id;
                $region12 = $city12DB->region_id;
            }

            $company12 = Company::create([
                'name' => 'Next Holiday Services',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region12,
                'country_id' => $country12,
                'city_id' => $city12,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'ozer@nextholidayservices.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc12 = DMC::create([
                "company_id" => $company12->id,
                // "is_parent",
                'name' => $company12->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region12,
                "country_id" => $country12,
                "city_id" => $city12,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => $company12->email,
                // "website",
        
                "reservation_email" => 'ozer@nextholidayservices.com',
            ]);


            $region13 = null;
            $country13 = null;
            $city13 = null;

            $city13DB = City::where('code', 'MRU')->first();
            if($city13DB)
            {
                $city13 = $city13DB->id;
                $country13 = $city13DB->country_id;
                $region13 = $city13DB->region_id;
            }

            $company13 = Company::create([
                'name' => 'Coquille Bonheur',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region13,
                'country_id' => $country13,
                'city_id' => $city13,
           
                // 'street',
                // 'postal_code',

                // "phone",
                // "fax",
                "email" => 'marketing@coquillebonheur.com',

                // 'latitude',
                // 'longitude',
                // 'location_link',
        
                // 'instagram',
                // 'twitter',
                // 'facebook',
                // 'linkedin',
            ]);

            $dmc13 = DMC::create([
                "company_id" => $company13->id,
                // "is_parent",
                'name' => $company13->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region13,
                "country_id" => $country13,
                "city_id" => $city13,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => $company13->email,
                // "website",
        
                // "reservation_email",
            ]);


            $region14 = null;
            $country14 = null;
            $city14 = null;

            $city14DB = City::where('code', 'MRU')->first();
            if($city14DB)
            {
                $city14 = $city14DB->id;
                $country14 = $city14DB->country_id;
                $region14 = $city14DB->region_id;
            }

            $company14 = Company::create([
                'name' => 'Hello Island',      
                // 'hotel_id',
                // 'rank',
                // 'contract',
         
                'company_type_id' => 6,
        
                'region_id' => $region14,
                'country_id' => $country14,
                'city_id' => $city14,
           
                // 'street',
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

            $dmc14 = DMC::create([
                "company_id" => $company14->id,
                // "is_parent",
                'name' => $company14->name,

                // "is_client",
                // "ref_id",
                // "has_hotels",
                // "has_golf_courses",
        
                // "active",
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => $region14,
                "country_id" => $country14,
                "city_id" => $city14,

                // "street",
                // "postal_code",
                // "phone",
                // "fax",
                "email" => $company14->email,
                // "website",
        
                // "reservation_email",
            ]);
        }
    }
}
