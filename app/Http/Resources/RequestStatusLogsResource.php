<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use App\Models\User;
use App\Models\RequestSubStatus;
use App\Models\RequestStatus;

class RequestStatusLogsResource extends JsonResource
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

            'old_sub_status_id' => $this->get_status_from_properties($this->properties, 'old','sub_status_id'),
            'old_sub_status_name' => $this->get_status_from_properties($this->properties, 'old','subStatus.name'),

            'sub_status_id' => $this->get_status_from_properties($this->properties, 'attributes','sub_status_id'),
            'sub_status_name' => $this->get_status_from_properties($this->properties, 'attributes','subStatus.name'),

            'is_system' => $this->is_system_action(),

            'causer_type' => $this->causer_type,
            'causer_id' => $this->causer_id,

            'user' => ($this->userData()) ? $this->userData()->details->first_name . ' ' . $this->userData()->details->last_name : '',
            'user_company_type_id' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->type->id : '') : '',
            'user_company_type_name' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->type->name : '') : '',
            'user_company_id' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->id : '') : '',
            'user_company_name' => ($this->userData()) ? (($this->userData()->details->company) ? $this->userData()->details->company->name : '') : '',
            "user_role_id" =>  ($this->userData()) ? (($this->userData()->details->role) ? $this->userData()->details->role->id : '') : '',
            
            'created_at' => $this->created_at,

            'properties' => $this->properties,
        ];
    }

    public function is_system_action()
    {
        $subStatusName = $this->get_status_from_properties($this->properties, 'attributes','subStatus.name');

        if (str_contains($subStatusName, 'Sys Redirected') || str_contains($subStatusName, 'SP Confirmed') )
        { 
            return 1;
        }

        return 0;
    }

    public function userData()
    {
        $user = User::find($this->causer_id);

        if($user)
        {
            return $user;
        }

        return false;
    }

    public function get_status_from_properties($properties, $attributeName, $key)
    {
        if(isset($properties[$attributeName]))
        {
            $attributes = $properties[$attributeName];
        
            if(isset($attributes[$key])){
    
                $attributeKey = $attributes[$key];
    
                if($key == 'subStatus.name')
                {
                    $requestSubStatus = RequestSubStatus::where('name', $attributeKey)->first();
                    if($requestSubStatus)
                    {
                        $requestStatus = RequestStatus::where('id', $requestSubStatus->request_status_id)->first();
                        if($requestStatus)
                        {
                            return $requestStatus->name . ' - ' . $attributeKey ;
                        }
                    }
                }
                return $attributeKey;
            }
        }
  
        return null;
    }
}
