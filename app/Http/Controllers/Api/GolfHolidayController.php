<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\GolfHoliday;
use App\Models\Product;
use App\Models\Hotel;
use App\Models\Tag;
use App\Http\Resources\GolfHolidayResource;
use DB;
use Carbon\Carbon;

class GolfHolidayController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $product = new GolfHoliday();
        
        $productsData = GolfHolidayResource::collection($product->get_pagination($filter));

        return response()->json([
            'status' => true,
            'products' => $productsData->response()->getData()
        ]);
    }

    public function get_all()
    {
        $filter = $this->prepare_filter(request());
      
        $product = new GolfHoliday();
        
        $productsData = GolfHolidayResource::collection($product->get_all());

        return response()->json([
            'status' => true,
            'products' => $productsData
        ]);
    }

    public function show($id)
    {
        $product = GolfHoliday::findOrFail($id);

        $productData = new GolfHolidayResource($product);

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
            
            'number_of_nights' => 'required|numeric',
            'number_of_guests' => 'required|numeric',
            'number_of_rounds' => 'required|numeric',
            'number_of_golf_courses' => 'required|numeric',
    
            'unlimited_rounds' => 'required|in:1,0',

            'products' => 'array',
            'products.*' => 'exists:products,id',

            'hotels' => 'array',
            'hotels.*' => 'exists:hotels,id',

            'tags' => 'array',
            'tags.*' => 'exists:tags,id',

            'status' => 'required|in:1,0',
    
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            $product = GolfHoliday::create($data);

            if(!$request->code)
            {
                
                $code = $product->service->code . $product->id;

                $product->update([
                    'code' => $code
                ]);
            }


            if(is_array($request->products) && count($request->products) > 0)
            {
                $productsData = Product::whereIn('id', $request->products)->get();
                foreach($productsData as $item)
                {
                    $product->products()->save($item);
                }
            }

            if(is_array($request->tags) && count($request->tags) > 0)
            {
                $tags = Tag::whereIn('id', $request->tags)->get();
                foreach($tags as $tag)
                {
                    $product->tags()->save($tag);
                }
            }

            if(is_array($request->hotels) && count($request->hotels) > 0)
            {
                $hotelsData = Hotel::whereIn('id', $request->hotels)->get();
                foreach($hotelsData as $item)
                {
                    $product->hotels()->save($item);
                }
            }

            DB::commit();

            $productData = new GolfHolidayResource($product);

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
        $product = GolfHoliday::findOrFail($id);

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
    
            'number_of_nights' => 'required|numeric',
            'number_of_guests' => 'required|numeric',
            'number_of_rounds' => 'required|numeric',
            'number_of_golf_courses' => 'required|numeric',
    
            'unlimited_rounds' => 'required|in:1,0',

            'products' => 'array',
            'products.*' => 'exists:products,id',

            'hotels' => 'array',
            'hotels.*' => 'exists:hotels,id',
            
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',

            'status' => 'required|in:1,0',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $product->update($data);

            if(is_array($request->products) && count($request->products) > 0)
            {
                $product->products()->detach();

                $productsData = Product::whereIn('id', $request->products)->get();
                foreach($productsData as $item)
                {
                    $product->products()->save($item);
                }
            }

            if(is_array($request->hotels) && count($request->hotels) > 0)
            {
                $product->hotels()->detach();

                $hotelsData = Hotel::whereIn('id', $request->hotels)->get();
                foreach($hotelsData as $item)
                {
                    $product->hotels()->save($item);
                }
            }

            if(is_array($request->tags))
            {
                $product->tags()->detach();

                $tags = Tag::whereIn('id', $request->tags)->get();
                foreach($tags as $tag)
                {
                    $product->tags()->save($tag);
                }
            }

            DB::commit();

            $productData = new GolfHolidayResource(GolfHoliday::find($product->id));

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
        $product = GolfHoliday::findOrFail($id);

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

        return $filter;
    }

}
