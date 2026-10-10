<?php

namespace App\Http\Controllers\Api\Integration;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Region;
use App\Http\Resources\RegionResource;

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
}
