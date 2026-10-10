<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TourOperator;
use App\Models\Company;
use DB;

class TourOperatorNewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
 
        
        if(TourOperator::count() == 0)
        {
             $op = TourOperator::create([
                "id" => 2,
                "company_id" => 1,
                // "is_parent",
                "name" => 'TUI Deutschland GmbH',
                "ref_id" => 'TUID',
                "has_gfp_requests" => 0,
                "active" => 1,
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => 2,
                "country_id" => 39,
                "city_id" => 177,
                "street" => 'Hanover, Lower Saxony',
                // "postal_code",
                // "phone",
                // "fax",
                // "email",
                // "website",
            ]);
            
            $op = TourOperator::create([
                "id" => 2000,
                "company_id" => 1,
                // "is_parent",
                "name" => 'Golf Globe TO',
                "ref_id" => 'ageg_id',
                "has_gfp_requests" => 0,
                "active" => 1,
        
                // "delegate_name",
                // "delegate_email",
                // "delegate_mobile_number",
                // "delegate_user_id",
                // "assigned_user_id",
        
                "region_id" => 2,
                "country_id" => 39,
                "city_id" => 188,
                "street" => 'Hanover, Lower Saxony',
                // "postal_code",
                // "phone",
                // "fax",
                // "email",
                // "website",
            ]);

           
        }

        
    }
}
