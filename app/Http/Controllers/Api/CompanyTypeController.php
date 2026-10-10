<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\CompanyType;
use App\Http\Resources\CompanyTypeResource;

class CompanyTypeController extends Controller
{
    public function index()
    {
        $types = new CompanyType();
        $typesData = CompanyTypeResource::collection($types->get_all());
        return response()->json([
            'status' => true,
            'types' => $typesData
        ]);
    }
}
