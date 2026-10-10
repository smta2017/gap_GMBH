<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class UserDetailsResource extends JsonResource
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
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'mobile_number' => $this->mobile_number,
            'fax' => $this->fax,
            'department' => $this->department,
            'title' => $this->title,
            'company_id' => $this->company_id,
            'company_name' => ($this->company) ? $this->company->name : '',

            'company_type_id' => ($this->company) ? $this->company->type->id : '',
            'company_type_name' => ($this->company) ? $this->company->type->name : '',

            'role_id' => $this->role_id,
            'role_name' => ($this->role) ? $this->role->name : '',
             
            'address_book_id' => $this->address_book_id,
            'lang' => $this->lang,
            
            'permissions' => ($this->role) ? PermissionResource::collection($this->role->permissions) : []
        ];
    }
}
