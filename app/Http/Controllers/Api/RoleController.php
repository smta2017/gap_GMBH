<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Role;
use App\Models\Permission;
use App\Http\Resources\RoleResource;
use DB;

class RoleController extends Controller
{
    public function index()
    {
        $filter = $this->prepare_filter(request());
      
        $roles = new Role();

        $rolesData = RoleResource::collection($roles->get_all($filter));

        return response()->json([
            'status' => true,
            'roles' => $rolesData
        ]);
    }

    public function show($id)
    {
        $role = Role::findOrFail($id);

        $roleData = new RoleResource($role);

        return response()->json([
            'status' => true,
            'role' => $roleData,
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required',
            'status' => 'required|numeric|in:1,0',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ]);


        try {
            DB::beginTransaction();

            $role = Role::create([
                'name' => $request->name,
                'status' => $request->status
            ]);

            $permissions = Permission::whereIn('id', $request->permissions)->get();
            foreach($permissions as $permission)
            {
                $role->permissions()->save($permission);
            }
            


            DB::commit();

            $roleData = new RoleResource($role);

            return response()->json([
                'status' => true,
                'role' => $roleData
            ]);
        } catch (\PDOException $e) {
            DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }

    public function assign_permissions_to_role($id, Request $request)
    {

        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            $permissions = Permission::whereIn('id', $request->permissions)->get();
            foreach($permissions as $permission)
            {
                $exists = $role->permissions->contains($permission->id);
                
                if(!$exists)
                {
                    $role->permissions()->save($permission);
                }
            }

            DB::commit();

            $roleData = new RoleResource(Role::find($role->id));

            return response()->json([
                'status' => true,
                'role' => $roleData
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
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required',
            'status' => 'required|numeric|in:1,0',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            DB::beginTransaction();

            $role->update([
                'name' => $request->name,
                'status' => $request->status
            ]);

            $role->permissions()->detach();

            $permissions = Permission::whereIn('id', $request->permissions)->get();
 
            
            foreach($permissions as $permission)
            {
                $role->permissions()->save($permission);
            }
            
            foreach ($role->users as $user_details) {
                $user_details->user->tokens()->delete();
            }
            
            DB::commit();

            $roleData = new RoleResource($role);

            return response()->json([
                'status' => true,
                'role' => $roleData
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
        $role = Role::findOrFail($id);

        try {
            DB::beginTransaction();

            $role->delete();

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
