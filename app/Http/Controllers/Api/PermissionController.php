<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Http\Resources\PermissionResource;
use DB;

class PermissionController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $permissions = new Permission();
        
        $permissionsData = PermissionResource::collection($permissions->get_all($filter));

        return response()->json([
            'status' => true,
            'permissions' => $permissionsData
        ]);
    }

    public function show($id)
    {
        $permission = Permission::findOrFail($id);

        $permissionData = new PermissionResource($permission);

        return response()->json([
            'status' => true,
            'permission' => $permissionData,
        ]);
    }
    
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:permissions,code',
            'description' => 'required',
            'status' => 'required|numeric|in:1,0',
            'module_id' => 'required|exists:modules,id',
            'page_id' => 'required|exists:pages,id',
        ]);


        try {
            DB::beginTransaction();

            $permission = Permission::create([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'status' => $request->status,
                'module_id' => $request->module_id,
                'page_id' => $request->page_id,
            ]);
 
            DB::commit();

            $permissionData = new PermissionResource($permission);

            return response()->json([
                'status' => true,
                'permission' => $permissionData
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
        $permission = Permission::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'code' => 'required|unique:permissions,code,' . $permission->id,
            'description' => 'required',
            'status' => 'required|numeric|in:1,0',
            'module_id' => 'required|exists:modules,id',
            'page_id' => 'required|exists:pages,id',
        ]);

        try {
            DB::beginTransaction();

            $permission->update([
                'name' => $request->name,
                'code' => $request->code,
                'description' => $request->description,
                'status' => $request->status,
                'module_id' => $request->module_id,
                'page_id' => $request->page_id,
            ]);

            DB::commit();

            $permissionData = new PermissionResource($permission);

            return response()->json([
                'status' => true,
                'permission' => $permissionData
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
        $permission = Permission::findOrFail($id);

        try {
            DB::beginTransaction();

            $permission->delete();

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
