<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Module;
use App\Http\Resources\ModuleResource;

class ModuleController extends Controller
{
    public function index()
    {
        $modules = new Module();
        $modulesData = ModuleResource::collection($modules->get_all());
        return response()->json([
            'status' => true,
            'modules' => $modulesData
        ]);
    }
}
