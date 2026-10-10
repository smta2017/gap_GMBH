<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Integration;
use App\Http\Resources\IntegrationResource;
use DB;

class IntegrationController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $integration = new Integration();
        
        $integrationsData = IntegrationResource::collection($integration->get_all($filter));

        return response()->json([
            'status' => true,
            'integrations' => $integrationsData
        ]);
    }

    public function show($id)
    {
        $int = Integration::findOrFail($id);

        $intData = new IntegrationResource($int);

        return response()->json([
            'status' => true,
            'integration' => $intData,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',

            'status' => 'required|in:0,1',
            'expiry_date' => 'required|date_format:Y-m-d',

            'company_id' => 'required|exists:companies,id',
    
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();

            $user = request()->user();

            $data['created_by'] = $user->id;

            $data['api_key'] = \Str::random(64);

            $int = Integration::create($data);

            DB::commit();

            $intData = new IntegrationResource($int);

            return response()->json([
                'status' => true,
                'int' => $intData
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
        $int = Integration::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'description' => 'required',

            'status' => 'required|in:0,1',
            'expiry_date' => 'required|date_format:Y-m-d',
        ]);

        try {
            DB::beginTransaction();

            $data = $request->all();
            
            $user = request()->user();

            $data['updated_by'] = $user->id;

            $int->update($data);

            DB::commit();

            $intData = new IntegrationResource(Integration::find($int->id));

            return response()->json([
                'status' => true,
                'integration' => $intData
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
        $int = Integration::findOrFail($id);

        try {
            DB::beginTransaction();


            $int->delete();

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
