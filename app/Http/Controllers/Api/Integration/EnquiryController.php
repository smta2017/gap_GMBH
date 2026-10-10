<?php

namespace App\Http\Controllers\Api\Integration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Enquiry;
use App\Models\Integration;
use App\Http\Resources\EnquiryResource;
use DB;


class EnquiryController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'arrival_date' => 'required',
            'group_number' => 'required',
            'number_of_nights' => 'required|numeric',
            'number_of_rounds' => 'required|numeric',
            
            'flight' => 'required|in:0,1',
            'receive_offer' => 'required|in:0,1',
    
            'first_name' => 'required',
            'last_name' => 'required',
            'mobile_number' => 'required',
            'email' => 'required',
    
            // 'city_id' => 'required|exists:cities,id',
    
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $integration = Integration::where('api_key', $request->bearerToken())->first();

            $data['integration_id'] = $integration->id;
            $data['company_id'] = $integration->company_id;
            $data['status_id'] = 1;

            $data['ip_address'] = request()->ip();
            
            $enquiry = Enquiry::create($data);

            DB::commit();

            $enquiryData = new EnquiryResource($enquiry);

            return response()->json([
                'status' => true,
                'enquiry' => $enquiryData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
