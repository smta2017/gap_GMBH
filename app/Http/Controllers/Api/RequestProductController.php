<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequestProduct;
use App\Models\RequestProductDetails;
use App\Models\Hole;
use App\Models\TeeTime;
use App\Http\Resources\RequestProductResource;
use DB;
use Carbon\Carbon;

class RequestProductController extends Controller
{
    public function show($id)
    {
        $product = RequestProduct::findOrFail($id);

        $productData = new RequestProductResource($product);

        return response()->json([
            'status' => true,
            'product' => $productData,
        ]);
    }

    public function update($id, Request $request)
    {
        $product = RequestProduct::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',

            'service_id' => 'required|exists:product_services,id',
            
            'golf_course_id' => 'required_unless:is_package,1|exists:golf_courses,id',
        
            'tee_time_id' => 'required|exists:tee_times,id',
            'hole_id' => 'required_unless:is_package,1|exists:holes,id',

            // 'code' => 'unique:products,code,' . $id,

            'validity_from' => 'required|date:y-m-d',
            'validity_to' => 'required|date:y-m-d',
    
            'status' => 'required|in:1,0',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $product->update($data);

            DB::commit();

            $productData = new RequestProductResource(RequestProduct::find($product->id));

            return response()->json([
                'status' => true,
                'product' => $productData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function update_details($id, Request $request)
    {
        $details = RequestProductDetails::findOrFail($id);

        $validated = $request->validate([

            "golf_course_id" => "required",
            "type" => "required",

            "tee_time_id" => "exists:tee_times,id",
            "min_tee_time_id" => "exists:tee_times,id",
            "max_tee_time_id" => "exists:tee_times,id"

        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $details->update($data);

            DB::commit();

            $productData = new RequestProductResource(RequestProduct::find($details->request_product_id));

            return response()->json([
                'status' => true,
                'product' => $productData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function delete_details($id, Request $request)
    {
        $details = RequestProductDetails::findOrFail($id);

        $request_product_id = $details->request_product_id;

        try {
            DB::beginTransaction();

            $details->forceDelete();

            DB::commit();

            $productData = new RequestProductResource(RequestProduct::find($request_product_id));

            return response()->json([
                'status' => true,
                'product' => $productData
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
