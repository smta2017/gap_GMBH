<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Hotel;
use App\Models\Company;
use App\Models\TourOperator;

class GroupHotelsWithTheSameCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $hotels = \DB::table('hotels')->groupBy('ref_id')->get();

        foreach($hotels as $hotel)
        {
            $sameHotelsCode = Hotel::where('ref_id', $hotel->ref_id)->where('id', '!=', $hotel->id)->get();

            foreach($sameHotelsCode as $sameHotel)
            {
                $Parentcompany = Company::where('id', $sameHotel->company_id)->first();

                if($Parentcompany && $Parentcompany->company_type_id == '5')
                {
                    $operators = TourOperator::where('company_id', $Parentcompany->id)->get();
                    foreach($operators as $oper)
                    {
                        $hoteOb = Hotel::find($hotel->id);
                        if($hoteOb)
                            $hoteOb->touroperators()->save($oper);
                    }

                    // remove Hotel
                    $sameHotel->forceDelete();

                    // Remove Parent Company Of Type Operator

                    if(!$Parentcompany->check_has_childs())
                    {
                        $Parentcompany->forceDelete();
                    }
                }
            }
        }
    }
}
