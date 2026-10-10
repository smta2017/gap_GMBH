<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\PriceList;
use App\Models\PriceListType;
use App\Http\Resources\PriceListResource;
use DB;

class PriceListController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $list = new PriceList();
        
        $listsData = PriceListResource::collection($list->get_all($filter));

        return response()->json([
            'status' => true,
            'lists' => $listsData
        ]);
    }

    public function get_types()
    {
        $types = PriceListType::where('status', '1')->select(['id', 'name'])->get();
  
        return response()->json([
            'status' => true,
            'types' => $types,
        ]);
    }

    public function show($id)
    {
        $list = PriceList::findOrFail($id);

        $listData = new PriceListResource($list);

        return response()->json([
            'status' => true,
            'list' => $listData,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'service_id' => 'required|exists:product_services,id',
            'price_list_type_id' => 'required|exists:price_list_types,id',
            'populate_list_id' => 'exists:price_lists,id',
            'markup' => 'numeric',
            'status' => 'in:0,1',
    
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            $list = PriceList::create($data);

            DB::commit();

            $listData = new PriceListResource($list);

            return response()->json([
                'status' => true,
                'list' => $listData
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
        $list = PriceList::findOrFail($id);

        $validated = $request->validate([
            'price_list_type_id' => 'required|exists:price_list_types,id',
            'populate_list_id' => 'exists:price_lists,id',
            'markup' => 'numeric',
            'status' => 'in:0,1',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $list->update($data);

            DB::commit();

            $listData = new PriceListResource(PriceList::find($list->id));

            return response()->json([
                'status' => true,
                'list' => $listData
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
        $list = PriceList::findOrFail($id);

        try {
            DB::beginTransaction();


            $list->delete();

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
