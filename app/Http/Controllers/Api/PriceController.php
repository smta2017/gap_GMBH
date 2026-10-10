<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Price;
use App\Http\Resources\PriceResource;
use DB;

class PriceController extends Controller
{   
    public function store(Request $request)
    {
        $validated = $request->validate([
            'service_id' => 'required|exists:product_services,id',
            'price_list_id' => 'required|exists:price_lists,id',
            'product_id' => 'required|exists:products,id',
            'season_id' => 'required|exists:seasons,id',
            'price' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            $price = Price::create($data);

            DB::commit();

            $priceData = new PriceResource($price);

            return response()->json([
                'status' => true,
                'price' => $priceData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }


    public function update($id, Request $request)
    {
        $price = Price::findOrFail($id);

        $validated = $request->validate([
            'price' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $price->update($data);

            DB::commit();

            $priceData = new PriceResource(Price::find($price->id));

            return response()->json([
                'status' => true,
                'price' => $priceData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function getFalseResponse()
    {
        return response()->json([
            'status' => false
        ], 422);
    }
}
