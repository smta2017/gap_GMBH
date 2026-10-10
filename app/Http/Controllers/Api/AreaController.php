<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\City;
use App\Models\Country;
use App\Models\Area;
use App\Models\Language;
use App\Http\Resources\AreaResource;
use App\Http\Resources\AreaResourceNew;
use App\Http\Resources\AreaResourceTable;
use Carbon\Carbon;
use DB;
use File;

class AreaController extends Controller
{
    public function index()
    {
        $c_id = request()->input('c_id');

        $cities = new Area();

        $citiesData = AreaResource::collection($cities->get_all());

        return response()->json([
            'status' => true,
            'areas' => $citiesData
        ]);
    }


    public function newIndex()
    {
        $c_id = request()->input('c_id');

        $cities = new Area();

        $citiesData = AreaResourceNew::collection($cities->get_all());

        return response()->json([
            'status' => true,
            'areas' => $citiesData
        ]);
    }

    public function tableIndex()
    {
        $c_id = request()->input('c_id');

        $cities = new Area();

        $citiesData = AreaResourceTable::collection($cities->get_all());

        return response()->json([
            'status' => true,
            'areas' => $citiesData
        ]);
    }

    public function show($id)
    {
        $city = Area::findOrFail($id);

        $cityData = new AreaResource($city);

        return response()->json([
            'status' => true,
            'area' => $cityData,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'name' => 'required',
            // 'code' => 'required',
            'status' => 'required|in:1,0',
            'city_id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $city = Area::create($request->all());

            $city->translations()->create([
                'language_id' => 1,
                'locale' => 'en',
                'name' => $request->name_en,
            ]);

            $city->translations()->create([
                'language_id' => 2,
                'locale' => 'de',
                'name' => $request->name_de,
            ]);
           
            DB::commit();

            $cityData = new AreaResource($city);

            return response()->json([
                'status' => true,
                'area' => $cityData
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
        $city = Area::findOrFail($id);

        $validated = $request->validate([
            // 'name' => 'required',
            // 'code' => 'required',
            'status' => 'required|in:1,0',
            'city_id' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $city->update([
                // 'name' => $request->name,
                'code' => $request->code,
                'status' => $request->status,
                'city_id' => $request->city_id,
            ]);

            if(isset($request->translations) && is_array($request->translations) && count($request->translations) > 0)
            {
                $city->translations()->forceDelete();
                foreach($request->translations as $translation)
                {
                    $language = Language::findOrFail($translation['language_id']);

                    $translateName = (isset($translation['name'])) ? $translation['name'] : null;

                    $city->translations()->create([
                        'language_id' => $language->id,
                        'locale' => $language->code,
                        'name' => $translateName,
                    ]);
                }

            }

            DB::commit();

            $cityData = new AreaResource(Area::find($city->id));

            return response()->json([
                'status' => true,
                'area' => $cityData
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
        $city = Area::findOrFail($id);

        try {
            DB::beginTransaction();

            $city->delete();

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
}
