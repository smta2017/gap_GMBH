<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class RoleResource extends JsonResource
{

    public static $wrap = '';
    
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
    */
    
    
    public function toArray($request)
    {    
        return [
            'id' => $this->id,
            'name' => $this->name,
            'status' => $this->status,
            'users_count' => $this->users->count(),
            'permissions_count' => $this->permissions->count(),
            'permissions' => PermissionResource::collection($this->permissions)
        ];
    }
}
