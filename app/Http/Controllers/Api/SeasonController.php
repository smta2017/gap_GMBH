<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Season;
use App\Models\ProductService;
use App\Http\Resources\SeasonResource;
use DB;

class SeasonController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $seasons = new Season();
        $seasons = $seasons->where($filter);

        if(isset(request()->company_id))
        {
            $services = ProductService::where('company_id', request()->company_id)->pluck('id')->toArray();
            $seasons = $seasons->whereIn('service_id', $services);
        }

        $seasonsData = SeasonResource::collection($seasons->get());

        return response()->json([
            'status' => true,
            'seasons' => $seasonsData
        ]);
    }

    public function show($id)
    {
        $season = Season::findOrFail($id);

        $seasonData = new SeasonResource($season);

        return response()->json([
            'status' => true,
            'season' => $seasonData,
        ]);
    }

    public function update($id, Request $request)
    {
        $season = Season::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required',
            'start_date' => 'string',
            'end_date' => 'string',
            'color' => 'string',
            'display' => 'string',
            'peak_time_from' => 'string',
            'peak_time_to' => 'string',
        ]);

        try {
            DB::beginTransaction();
            $season->update($request->all());
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

    public function destroy($id)
    {
        $season = Season::findOrFail($id);

        try {
            DB::beginTransaction();

            $season->delete();

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
