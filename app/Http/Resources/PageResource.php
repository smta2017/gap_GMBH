<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PageResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
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
            'sort' => $this->sort,
            'module_id' => $this->module_id,
            'module_name' => $this->module->name,
            'children_key' => 'permissions',
            'permissions' => PermissionResource::collection($this->permissions)
        ];
    }
}
