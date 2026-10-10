<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Models\Language;
use App\Http\Resources\RegionResource;
use App\Http\Resources\RegionResourceNew;
use App\Http\Resources\RegionResourceTable;
use App\Imports\RegionImport;
use App\Models\Area;
use App\Models\City;
use App\Models\Country;
use DB;
use Maatwebsite\Excel\Facades\Excel;

class RegionController extends Controller
{
    public function index()
    {
        $regions = new Region();
        $regionsData = RegionResource::collection($regions->get_all());
        return response()->json([
            'status' => true,
            'regions' => $regionsData
        ]);
    }

    public function newIndex()
    {
        $regions = new Region();
        $regionsData = RegionResourceNew::collection($regions->get_all());
        return response()->json([
            'status' => true,
            'regions' => $regionsData
        ]);
    }

    public function tableIndex()
    {
        $regions = new Region();
        $regionsData = RegionResourceTable::collection($regions->get_all());
        return response()->json([
            'status' => true,
            'regions' => $regionsData
        ]);
    }
    public function index_info()
    {
        $regions = new Region();
        $regionsData = RegionResource2::collection($regions->get_all());
        return response()->json([
            'status' => true,
            'regions' => $regionsData
        ]);
    }

    public function show($id)
    {
        $region = Region::findOrFail($id);

        $regionData = new RegionResource($region);

        return response()->json([
            'status' => true,
            'region' => $regionData,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            // 'name' => 'required',
            'code' => 'required',
            'status' => 'required|in:1,0',
        ]);

        try {
            DB::beginTransaction();

            $region = Region::create([
                'name' => $request->name,
                'code' => $request->code,
                'status' => $request->status
            ]);

            if(isset($request->translations) && is_array($request->translations) && count($request->translations) > 0)
            {
                foreach($request->translations as $translation)
                {
                    $language = Language::findOrFail($translation['language_id']);

                    $translateName = (isset($translation['name'])) ? $translation['name'] : null;

                    $region->translations()->create([
                        'language_id' => $language->id,
                        'locale' => $language->code,
                        'name' => $translateName,
                    ]);
                }

            }

            DB::commit();

            $regionData = new RegionResource($region);

            return response()->json([
                'status' => true,
                'region' => $regionData
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
        $region = Region::findOrFail($id);

        $validated = $request->validate([
            // 'name' => 'required',
            'code' => 'required',
            'status' => 'required|in:1,0',
        ]);

        try {
            DB::beginTransaction();

            $region->update([
                'name' => $request->name,
                'code' => $request->code,
                'status' => $request->status
            ]);

            if(isset($request->translations) && is_array($request->translations) && count($request->translations) > 0)
            {
                $region->translations()->forceDelete();
                foreach($request->translations as $translation)
                {
                    $language = Language::findOrFail($translation['language_id']);

                    $translateName = (isset($translation['name'])) ? $translation['name'] : null;

                    $region->translations()->create([
                        'language_id' => $language->id,
                        'locale' => $language->code,
                        'name' => $translateName,
                    ]);
                }
            }

            DB::commit();

            $regionData = new RegionResource($region);

            return response()->json([
                'status' => true,
                'region' => $regionData
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
        $region = Region::findOrFail($id);

        try {
            DB::beginTransaction();

            $region->delete();

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

     

    public function import(Request $request)
    {
        $region = Excel::toCollection(new RegionImport, public_path('countries_cities.xlsx'));

        foreach ($region[0] as $key=> $row) {
            if ($key) {

                    $city = City::whereCode($row[6])->first();
                    if (!$city) {

                        $country = Country::whereCode($row[3])->first();
                        if (!$country) {

                            $reagion = Region::whereCode( $row[0])->first();
                            if (!$reagion) {
                                
                                $reagion = Region::create(['code' => $row[0], 'status' => 1]);
            
                                $reagion->translations()->create([
                                    'language_id' => 1,
                                    'locale' => 'en',
                                    'name' => $row[2],
                                ]);
                                $reagion->translations()->create([
                                    'language_id' => 2,
                                    'locale' => 'de',
                                    'name' => $row[1],
                                ]);
            
                            }

                            $country = Country::create(['code' => $row[3], 'region_id'=>$reagion->id, 'status' => 1]);

                            $country->translations()->create([
                                'language_id' => 1,
                                'locale' => 'en',
                                'name' => $row[5],
                            ]);
                            $country->translations()->create([
                                'language_id' => 2,
                                'locale' => 'de',
                                'name' => $row[4],
                            ]);

                        }
                        $city = City::create(['code' => $row[6], 'region_id'=>$reagion->id, 'country_id'=> $country->id, 'status' => 1]);
                        $city->translations()->create([
                            'language_id' => 1,
                            'locale' => 'en',
                            'name' => $row[8],
                        ]);
                        $city->translations()->create([
                            'language_id' => 2,
                            'locale' => 'de',
                            'name' => $row[7],
                        ]);
                    }

                    if($row[9]){
                        $area = Area::create(['code' => '', 'city_id'=>$city->id, 'region_id'=>$reagion->id, 'country_id'=> $country->id, 'status' => 1]);
                        $area->translations()->create([
                            'language_id' => 1,
                            'locale' => 'en',
                            'name' => $row[10],
                        ]);
                        $area->translations()->create([
                            'language_id' => 2,
                            'locale' => 'de',
                            'name' => $row[9],
                        ]);
                    }
                }
            }
    }


    public function checkRegionUpdated()
    {
        $a = \DB::select('
            select max(updated_at) as last_updated from (select max(updated_at) as updated_at from areas
            union 
            select max(updated_at) as updated_at from countries
            union
            select max(updated_at) as updated_at from cities
            union
            select max(updated_at) as updated_at from regions
            ) as updated_at;
        ')[0];
 
        return $a;
    }
}
