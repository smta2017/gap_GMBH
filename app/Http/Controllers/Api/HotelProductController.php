<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HotelProduct;
use App\Http\Resources\HotelProductResource;
use DB;
use Carbon\Carbon;

class HotelProductController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $product = new HotelProduct();
        
        $productsData = HotelProductResource::collection($product->get_pagination($filter));

        return response()->json([
            'status' => true,
            'products' => $productsData->response()->getData()
        ]);
    }

    public function get_all()
    {
        $filter = $this->prepare_filter(request());
      
        $product = new HotelProduct();
        
        $productsData = HotelProductResource::collection($product->get_all());

        return response()->json([
            'status' => true,
            'products' => $productsData
        ]);
    }

    public function show($id)
    {
        $product = HotelProduct::findOrFail($id);

        $productData = new HotelProductResource($product);

        return response()->json([
            'status' => true,
            'product' => $productData,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',

            'service_id' => 'required|exists:product_services,id',
            'hotel_id' => 'required|exists:hotels,id',
    
            // 'code' => 'unique:products,code',

            'room_type_id' => 'required|exists:room_types,id',
            'room_view_id' => 'required|exists:room_views,id',
            'room_board_id' => 'required|exists:room_boards,id',

            'validity_from' => 'required|date:y-m-d',
            'validity_to' => 'required|date:y-m-d',
    
            'status' => 'required|in:1,0',
    
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            $product = HotelProduct::create($data);

            if(!$request->code)
            {
                
                $code = $product->service->code . $product->id;

                $product->update([
                    'code' => $code
                ]);
            }

            DB::commit();

            $productData = new HotelProductResource($product);

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

    public function update($id, Request $request)
    {
        $product = HotelProduct::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',

            'service_id' => 'required|exists:product_services,id',
            'hotel_id' => 'required|exists:hotels,id',

            // 'code' => 'unique:products,code,' . $id,

            'room_type_id' => 'required|exists:room_types,id',
            'room_view_id' => 'required|exists:room_views,id',
            'room_board_id' => 'required|exists:room_boards,id',

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

            $productData = new HotelProductResource(HotelProduct::find($product->id));

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

    public function destroy($id)
    {
        $product = HotelProduct::findOrFail($id);

        try {
            DB::beginTransaction();

            $product->delete();

            DB::commit();

            return response()->json([
                'status' => true,
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function prepare_filter($request)
    {
        $filter = [];

        if($request->search)
        {
            array_push($filter, array('name', 'LIKE', '%' . $request->search . '%'));
        }

        if($request->service_id)
        {
            array_push($filter, array('service_id', $request->service_id ));
        }

        return $filter;
    }

}
